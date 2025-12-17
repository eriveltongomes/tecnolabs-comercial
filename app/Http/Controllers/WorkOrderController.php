<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\User;
use App\Models\Client;
use App\Models\ChecklistModel;
use App\Models\WorkOrderChecklist;
use App\Models\Settings\Equipment; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 

// IMPORTS PARA NOTIFICAÇÃO
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewWorkOrderAssigned;
use App\Mail\WorkOrderToPilot;

class WorkOrderController extends Controller
{
    // --- GESTÃO (BACKOFFICE) ---

    public function index(Request $request)
    {
        $query = WorkOrder::with(['client', 'technician', 'proposal']);
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        $workOrders = $query->orderByRaw("FIELD(status, 'pendente', 'em_execucao', 'agendada', 'concluida', 'cancelada')")
                            ->latest()
                            ->get();

        return view('work-orders.index', compact('workOrders'));
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['client', 'technician', 'equipments', 'checklists.checklistModel', 'checklists.user']);
        return view('work-orders.show', compact('workOrder'));
    }

    public function create()
    {
        $clients = Client::all();
        return view('work-orders.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'service_type' => 'required|in:drone,timelapse,tour_virtual,manutencao,teste,outros',
            'description' => 'nullable|string',
            'service_location' => 'required|string|max:255',
        ]);

        $os = WorkOrder::create([
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'service_type' => $data['service_type'],
            'description' => $data['description'],
            'service_location' => $data['service_location'],
            'status' => 'pendente',
            'proposal_id' => null
        ]);

        // --- NOTIFICAÇÃO: NOVA OS (Para o Gestor) ---
        // Se foi criada manualmente e está pendente (sem técnico), avisa o Admin
        try {
            $gestores = User::where('role', 'admin')->get();
            foreach ($gestores as $gestor) {
                Mail::to($gestor->email)->send(new NewWorkOrderAssigned($os));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erro email nova OS: " . $e->getMessage());
        }

        return redirect()->route('work-orders.edit', $os->id)->with('success', 'OS criada! Agora defina os checklists.');
    }

    public function edit(WorkOrder $workOrder)
    {
        $technicians = User::whereIn('role', ['admin', 'tecnico', 'comercial'])->get();
        $workOrder->load(['checklists.checklistModel', 'equipments']);
        $availableModels = ChecklistModel::where('is_active', true)->get();
        $allEquipments = Equipment::all(); 
        
        return view('work-orders.edit', compact('workOrder', 'technicians', 'availableModels', 'allEquipments'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'technician_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'decea_protocol' => 'nullable|string|max:255',
            'flight_max_altitude' => 'nullable|integer',
            'description' => 'nullable|string', 
            'status' => 'required|in:pendente,agendada,em_execucao,concluida,cancelada',
            'equipments' => 'nullable|array',
            'equipments.*' => 'exists:settings_equipment,id'
        ]);

        // Guarda o técnico anterior para saber se mudou
        $oldTechnicianId = $workOrder->technician_id;

        if ($workOrder->status === 'pendente' && $data['status'] === 'pendente') {
            $data['status'] = 'agendada';
        }

        $workOrder->update([
            'technician_id' => $data['technician_id'],
            'scheduled_at' => $data['scheduled_at'],
            'decea_protocol' => $data['decea_protocol'],
            'flight_max_altitude' => $data['flight_max_altitude'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        if (isset($data['equipments'])) {
            $workOrder->equipments()->sync($data['equipments']);
        } else {
            $workOrder->equipments()->detach();
        }

        // --- NOTIFICAÇÃO: ESCALAÇÃO DE PILOTO ---
        // Se um técnico foi atribuído ou alterado
        if ($workOrder->technician_id && $workOrder->technician_id != $oldTechnicianId) {
            try {
                $piloto = User::find($workOrder->technician_id);
                if ($piloto) {
                    // 1. Gera e salva o PDF temporariamente
                    $workOrder->load(['client', 'technician', 'equipments', 'checklists.checklistModel', 'proposal']);
                    $pdf = Pdf::loadView('work-orders.pdf', compact('workOrder'));
                    $pdf->setPaper('a4', 'portrait');
                    
                    // Salva na pasta public/temp
                    $fileName = 'os-' . $workOrder->id . '.pdf';
                    Storage::put('public/temp/' . $fileName, $pdf->output());
                    $pdfPath = storage_path('app/public/temp/' . $fileName);

                    // 2. Envia o e-mail com anexo
                    Mail::to($piloto->email)->send(new WorkOrderToPilot($workOrder, $pdfPath));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erro email piloto: " . $e->getMessage());
            }
        }

        return redirect()->route('work-orders.index')->with('success', 'OS atualizada com sucesso!');
    }
    
    public function destroy(WorkOrder $workOrder)
    {
        if (Auth::user()->role !== 'admin') abort(403, 'Apenas administradores podem excluir Ordens de Serviço.');
        $workOrder->delete();
        return redirect()->route('work-orders.index')->with('success', 'OS excluída.');
    }

    // --- EXECUÇÃO (FRONTEND PILOTO) ---

    public function myServices()
    {
        $user = Auth::user();
        $workOrders = WorkOrder::where('technician_id', $user->id)
                        ->whereIn('status', ['agendada', 'em_execucao']) 
                        ->orderBy('scheduled_at', 'asc')
                        ->get();
                        
        return view('work-orders.my-services', compact('workOrders'));
    }

    public function execute(WorkOrder $workOrder)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $workOrder->technician_id !== $user->id) {
            abort(403, 'Você não é o técnico responsável por esta OS.');
        }

        $workOrder->load([
            'client', 
            'equipments', 
            'checklists.checklistModel.items', 
            'checklists.user',
            'checklists.answers.checklistItem' => function($query) {
                $query->withTrashed(); 
            }
        ]);

        return view('work-orders.execute', compact('workOrder'));
    }

    public function saveChecklist(Request $request, $checklistId)
    {
        $checklist = WorkOrderChecklist::findOrFail($checklistId);
        $request->validate(['answers' => 'required|array', 'risk_level' => 'nullable|in:baixo,medio,alto']);

        foreach ($request->answers as $itemId => $data) {
            $checklist->answers()->create([
                'checklist_item_id' => $itemId,
                'is_ok' => isset($data['ok']) && $data['ok'] == '1',
                'observation' => $data['obs'] ?? null
            ]);
        }

        $checklist->update([
            'filled_at' => now(),
            'user_id' => Auth::id(),
            'risk_level' => $request->risk_level ?? null,
            'comments' => $request->comments ?? null,
        ]);

        return redirect()->back()->with('success', 'Checklist salvo com sucesso!');
    }

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        $status = $request->status; 
        
        if ($status === 'em_execucao') {
            if ($workOrder->checklists()->whereNull('filled_at')->count() > 0) {
                return redirect()->back()->with('error', 'Segurança: Preencha todos os checklists (ARO/Pré-Voo) ANTES de iniciar.');
            }
            $workOrder->update(['status' => 'em_execucao', 'started_at' => now()]);
            return redirect()->back()->with('success', 'Serviço INICIADO! Bom voo.');
        }
        
        if ($status === 'concluida') {
            if ($workOrder->checklists()->whereNull('filled_at')->count() > 0) {
                return redirect()->back()->with('error', 'Checklists pendentes.');
            }
            $workOrder->update(['status' => 'concluida', 'finished_at' => now()]);
            return redirect()->route('work-orders.myServices')->with('success', 'Serviço FINALIZADO com sucesso!');
        }

        return redirect()->back();
    }

    // --- GERAÇÃO DE PDF ---


    // eri

    public function generateChecklistPdf($checklistId)
    {
        $checklist = WorkOrderChecklist::with([
            'workOrder.client', 'workOrder.technician', 'workOrder.equipments', 
            'checklistModel', 'user',
            'answers.checklistItem' => function($query) { $query->withTrashed(); }
        ])->findOrFail($checklistId);

        if (!$checklist->filled_at) return redirect()->back()->with('error', 'Este checklist ainda não foi preenchido.');

        $pdf = Pdf::loadView('work-orders.aro-pdf', compact('checklist'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('ARO_' . $checklist->workOrder->id . '.pdf');
    }

    // NOVO: PDF da OS
    public function generatePdf(WorkOrder $workOrder)
    {
        $workOrder->load(['client', 'technician', 'equipments', 'checklists.checklistModel', 'proposal']);
        $pdf = Pdf::loadView('work-orders.pdf', compact('workOrder'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('OS_' . $workOrder->id . '.pdf');
    }

    // --- GESTÃO DE VÍNCULOS ---

    public function addChecklist(Request $request, WorkOrder $workOrder)
    {
        $request->validate(['checklist_model_id' => 'required|exists:checklist_models,id']);
        WorkOrderChecklist::create([
            'work_order_id' => $workOrder->id,
            'checklist_model_id' => $request->checklist_model_id,
            'filled_at' => null,
            'user_id' => null
        ]);
        return redirect()->back()->with('success', 'Checklist adicionado.');
    }

    public function removeChecklist($checklistId)
    {
        $checklist = WorkOrderChecklist::findOrFail($checklistId);
        if ($checklist->filled_at) return redirect()->back()->with('error', 'Não pode remover checklist já preenchido.');
        $checklist->delete();
        return redirect()->back()->with('success', 'Checklist removido.');
    }
}