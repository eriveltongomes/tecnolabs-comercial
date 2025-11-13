<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Client;
use App\Models\Settings\Channel;
use App\Models\Settings\CommissionRule;
use App\Models\Settings\Equipment;
use App\Models\Settings\Course;
use App\Models\Settings\FixedCost;
use App\Models\Settings\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProposalController extends Controller
{
    // --- LISTAGEM ---
    public function index()
    {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'financeiro'])) {
            $query = Proposal::with(['client', 'user', 'channel']);
        } else {
            $query = Proposal::with(['client', 'channel'])->where('user_id', $user->id);
        }

        // Filtro de Status (para o Financeiro ver só "em_analise")
        if (request()->has('status') && !empty(request()->status)) {
            $query->where('status', request()->status);
        }

        $proposals = $query->latest()->get();
        return view('proposals.index', compact('proposals'));
    }

    // --- CRIAÇÃO ---
    public function create()
    {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'financeiro'])) {
            $clients = Client::all();
        } else {
            $clients = Client::where('created_by_user_id', $user->id)->get();
        }
        $channels = Channel::all();
        $equipments = Equipment::all();

        return view('proposals.create', compact('clients', 'channels', 'equipments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'channel_id' => 'required|exists:settings_channels,id',
            'service_type' => 'required|in:drone,timelapse,tour_virtual',
            'profit_margin' => 'required', 
            'details' => 'nullable|array',
            'variable_costs' => 'nullable|array',
            'total_value' => 'required',
            // Campos do Documento
            'service_location' => 'required|string|max:255',
            'service_date' => 'required|date',
            'payment_terms' => 'required|string|max:500',
            'courtesy' => 'nullable|string|max:255',
            'scope_description' => 'required|string|max:5000',
        ]);

        $proposal = new Proposal();
        $proposal->proposal_number = date('Y') . '-' . str_pad(Proposal::count() + 1, 4, '0', STR_PAD_LEFT); 
        $proposal->user_id = Auth::id();
        $proposal->client_id = $data['client_id'];
        $proposal->channel_id = $data['channel_id'];
        $proposal->service_type = $data['service_type'];
        
        // Preenche dados do documento
        $proposal->service_location = $data['service_location'];
        $proposal->service_date = $data['service_date'];
        $proposal->payment_terms = $data['payment_terms'];
        $proposal->courtesy = $data['courtesy'];
        $proposal->scope_description = $data['scope_description'];
        
        // Limpeza de valores monetários
        $cleanDetails = $data['details'] ?? [];
        if(isset($cleanDetails['labor_cost'])) $cleanDetails['labor_cost'] = $this->parseMoney($cleanDetails['labor_cost']);
        if(isset($cleanDetails['monthly_cost'])) $cleanDetails['monthly_cost'] = $this->parseMoney($cleanDetails['monthly_cost']);
        if(isset($cleanDetails['installation_cost'])) $cleanDetails['installation_cost'] = $this->parseMoney($cleanDetails['installation_cost']);
        
        $proposal->service_details = $cleanDetails;
        $proposal->total_value = $this->parseMoney($data['total_value']);
        $proposal->status = 'rascunho';
        $proposal->save();

        // Salva custos variáveis
        if (!empty($request->variable_costs)) {
            foreach ($request->variable_costs as $desc => $val) {
                $numericVal = $this->parseMoney($val);
                if ($numericVal > 0) {
                    $proposal->variableCosts()->create(['description' => $desc, 'cost' => $numericVal]);
                }
            }
        }
        
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Criou a proposta');

        return redirect()->route('proposals.index')->with('success', 'Proposta criada com sucesso!');
    }

    // --- EDIÇÃO ---
    public function edit(Proposal $proposal)
    {
        $this->authorizeEdit($proposal);
        
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'financeiro'])) {
            $clients = Client::all();
        } else {
            $clients = Client::where('created_by_user_id', $user->id)->get();
        }
        $channels = Channel::all();
        $equipments = Equipment::all();

        $variableCosts = [];
        foreach($proposal->variableCosts as $vc) {
            $variableCosts[$vc->description] = $vc->cost; 
        }

        return view('proposals.edit', compact('proposal', 'clients', 'channels', 'variableCosts', 'equipments'));
    }

    public function update(Request $request, Proposal $proposal)
    {
        $this->authorizeEdit($proposal);

        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'channel_id' => 'required|exists:settings_channels,id',
            'service_type' => 'required|in:drone,timelapse,tour_virtual',
            'status' => 'required|in:rascunho,aberta,em_analise,cancelada',
            'profit_margin' => 'required', 
            'details' => 'nullable|array',
            'variable_costs' => 'nullable|array',
            'total_value' => 'required',
            'service_location' => 'required|string|max:255',
            'service_date' => 'required|date',
            'payment_terms' => 'required|string|max:500',
            'courtesy' => 'nullable|string|max:255',
            'scope_description' => 'required|string|max:5000',
        ]);

        $cleanDetails = $data['details'] ?? [];
        if(isset($cleanDetails['labor_cost'])) $cleanDetails['labor_cost'] = $this->parseMoney($cleanDetails['labor_cost']);
        if(isset($cleanDetails['monthly_cost'])) $cleanDetails['monthly_cost'] = $this->parseMoney($cleanDetails['monthly_cost']);
        if(isset($cleanDetails['installation_cost'])) $cleanDetails['installation_cost'] = $this->parseMoney($cleanDetails['installation_cost']);

        $proposal->client_id = $data['client_id'];
        $proposal->channel_id = $data['channel_id'];
        $proposal->service_type = $data['service_type'];
        $proposal->status = $data['status']; 
        $proposal->service_location = $data['service_location'];
        $proposal->service_date = $data['service_date'];
        $proposal->payment_terms = $data['payment_terms'];
        $proposal->courtesy = $data['courtesy'];
        $proposal->scope_description = $data['scope_description'];
        $proposal->service_details = $cleanDetails;
        $proposal->total_value = $this->parseMoney($data['total_value']);
        
        if ($proposal->isDirty('status') && $proposal->getOriginal('status') == 'reprovada') {
            $proposal->rejection_reason = null;
        }
        
        $proposal->save();

        $proposal->variableCosts()->delete();
        if (!empty($request->variable_costs)) {
            foreach ($request->variable_costs as $desc => $val) {
                $numericVal = $this->parseMoney($val);
                if ($numericVal > 0) {
                    $proposal->variableCosts()->create(['description' => $desc, 'cost' => $numericVal]);
                }
            }
        }

        return redirect()->route('proposals.index')->with('success', 'Proposta atualizada com sucesso!');
    }

    // --- VISUALIZAÇÃO (SHOW) ---
    public function show(Proposal $proposal)
    {
        $this->authorizeView($proposal);

        // CÁLCULO DE CUSTOS OCULTOS (PARA EXIBIR NA TELA)
        $totalMonthlyFixed = FixedCost::sum('monthly_value');
        $fixedCostPerHour = $totalMonthlyFixed > 0 ? ($totalMonthlyFixed / 192) : 0;
        
        $details = $proposal->service_details;
        $hours = floatval($details['period_hours'] ?? 0);
        
        $costFixedProporcional = 0;
        $costEquipProporcional = 0;

        if ($proposal->service_type != 'timelapse') {
            // Rateio Fixo
            $costFixedProporcional = $fixedCostPerHour * $hours;

            // Depreciação Equipamento Específico
            if (!empty($details['equipment_id'])) {
                $equipment = Equipment::find($details['equipment_id']);
                if ($equipment && $equipment->lifespan_hours > 0) {
                    $hourlyDepreciation = $equipment->invested_value / $equipment->lifespan_hours;
                    $costEquipProporcional = $hourlyDepreciation * $hours;
                }
            }
        }

        return view('proposals.show', compact('proposal', 'costFixedProporcional', 'costEquipProporcional'));
    }

    // --- GERAÇÃO DE PDF ---
    public function generatePdf(Proposal $proposal)
    {
        $this->authorizeView($proposal);
        $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Proposta_' . $proposal->proposal_number . '.pdf');
    }

    // --- CÁLCULADORA (AJAX) ---
    public function calculate(Request $request)
    {
        $serviceType = $request->input('service_type');
        $details = $request->input('details', []);
        $vars = $request->input('variable_costs', []);
        $channelId = $request->input('channel_id');
        $profitMargin = $this->parseMoney($request->input('profit_margin', 0));

        $totalVariable = 0;
        foreach ($vars as $cost) { $totalVariable += $this->parseMoney($cost); }

        // Hora Técnica Base (Custos Fixos + Depreciação Média de Cursos)
        $totalMonthlyFixed = FixedCost::sum('monthly_value');
        $fixedCostPerHour = $totalMonthlyFixed > 0 ? ($totalMonthlyFixed / 192) : 0;

        // Cursos (Sempre soma todos, conhecimento acumulado)
        $courses = Course::all();
        $courseDepreciationHour = 0;
        foreach($courses as $cs) {
            if ($cs->lifespan_hours > 0) $courseDepreciationHour += ($cs->invested_value / $cs->lifespan_hours);
        }

        // Equipamento (Específico selecionado no form)
        $equipDepreciationHour = 0;
        if (!empty($details['equipment_id'])) {
            $equipment = Equipment::find($details['equipment_id']);
            if ($equipment && $equipment->lifespan_hours > 0) {
                $equipDepreciationHour = ($equipment->invested_value / $equipment->lifespan_hours);
            }
        }

        $totalHourlyCostBase = $fixedCostPerHour + $equipDepreciationHour + $courseDepreciationHour;

        $serviceCost = 0;
        if ($serviceType === 'drone' || $serviceType === 'tour_virtual') {
            $hours = floatval($details['period_hours'] ?? 1);
            $laborCost = $this->parseMoney($details['labor_cost'] ?? 0);
            $serviceCost = $laborCost + ($totalHourlyCostBase * $hours);
        } elseif ($serviceType === 'timelapse') {
            $months = floatval($details['months'] ?? 1);
            $monthlyCost = $this->parseMoney($details['monthly_cost'] ?? 0);
            $installCost = $this->parseMoney($details['installation_cost'] ?? 0);
            $serviceCost = ($monthlyCost * $months) + $installCost;
        }

        $totalCost = $serviceCost + $totalVariable;
        $taxes = Tax::where('is_default', true)->sum('percentage');
        
        // Lógica de Comissão Acumulativa
        $estimatedPrice = $totalCost * 1.5;
        $commissionPercent = $this->getCommissionPercentageBasedOnHistory(Auth::id(), $channelId, $estimatedPrice);

        $divisor = 1 - (($taxes + $commissionPercent + $profitMargin) / 100);
        $finalPrice = ($divisor <= 0) ? $totalCost * 2 : $totalCost / $divisor;
        $commissionValue = $finalPrice * ($commissionPercent / 100);

        return response()->json([
            'total_cost' => round($totalCost, 2),
            'taxes_percent' => $taxes,
            'commission_percent' => $commissionPercent,
            'commission_value' => round($commissionValue, 2),
            'final_price' => round($finalPrice, 2),
        ]);
    }

    // --- AÇÕES DE FLUXO ---
    
    public function sendToAnalysis(Proposal $proposal) {
        $this->authorizeEdit($proposal); 
        $proposal->update(['status' => 'em_analise']);
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Enviou para análise');
        return redirect()->route('proposals.index')->with('success', 'Enviada para análise!');
    }

    public function cancel(Proposal $proposal) {
        $this->authorizeEdit($proposal);
        $proposal->update(['status' => 'cancelada']);
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Cancelou a proposta');
        return redirect()->route('proposals.index')->with('success', 'Cancelada.');
    }

    public function refuse(Proposal $proposal) {
        $this->authorizeEdit($proposal);
        $proposal->update(['status' => 'recusada']);
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Marcou como Recusada pelo Cliente');
        return redirect()->route('proposals.index')->with('success', 'Proposta RECUSADA pelo cliente.');
    }

    public function approve(Proposal $proposal) {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'financeiro'])) abort(403);
        
        $finalValue = $proposal->total_value;
        $commissionPercent = $this->getCommissionPercentageBasedOnHistory($proposal->user_id, $proposal->channel_id, $finalValue);
        $commissionValue = $finalValue * ($commissionPercent / 100);

        $proposal->update([
            'status' => 'aprovada',
            'commission_value' => $commissionValue,
            'approved_by_user_id' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => null
        ]);
        
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Aprovou a proposta. Comissão: R$ ' . $commissionValue);

        return redirect()->back()->with('success', 'Proposta APROVADA!');
    }

    public function reject(Request $request, Proposal $proposal) {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'financeiro'])) abort(403);
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        
        $proposal->update([
            'status' => 'reprovada',
            'rejection_reason' => $request->rejection_reason,
            'commission_value' => null,
            'approved_by_user_id' => null,
            'approved_at' => null
        ]);
        
        if (function_exists('activity')) activity()->performedOn($proposal)->withProperties(['motivo' => $request->rejection_reason])->log('Reprovou a proposta');

        return redirect()->back()->with('success', 'Reprovada.');
    }

    public function reverseApproval(Request $request, Proposal $proposal) {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'financeiro'])) abort(403);
        $request->validate(['cancellation_reason' => 'required|string|max:500']);

        $proposal->update([
            'status' => 'cancelada',
            'commission_value' => 0.00,
            'rejection_reason' => 'ESTORNO: ' . $request->cancellation_reason,
            'approved_by_user_id' => null,
            'approved_at' => null
        ]);

        if (function_exists('activity')) activity()->performedOn($proposal)->withProperties(['motivo' => $request->cancellation_reason])->log('REALIZOU ESTORNO (Cancelou venda aprovada)');

        return redirect()->back()->with('success', 'Venda estornada com sucesso!');
    }

    public function destroy(Proposal $proposal) {
        $this->authorizeEdit($proposal); 
        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Deletado.');
    }

    // --- HELPER METHODS ---

    private function getCommissionPercentageBasedOnHistory($userId, $channelId, $currentValue) {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlySales = Proposal::where('user_id', $userId)->where('status', 'aprovada')->whereBetween('approved_at', [$startOfMonth, $endOfMonth])->sum('total_value');
        $projectedTotal = $monthlySales + $currentValue;
        
        $commissionRule = CommissionRule::where('channel_id', $channelId)
            ->whereHas('revenueTier', function($q) use ($projectedTotal) {
                $q->where('min_value', '<=', $projectedTotal)->where('max_value', '>=', $projectedTotal);
            })->first();
            
        if (!$commissionRule) {
            $commissionRule = CommissionRule::where('channel_id', $channelId)->orderByDesc('percentage')->first();
        }
        return $commissionRule ? $commissionRule->percentage : 0;
    }

    private function parseMoney($value) {
        if (empty($value)) return 0;
        if (is_numeric($value)) return floatval($value);
        $clean = str_replace(['.', 'R$', ' '], '', $value);
        $clean = str_replace(',', '.', $clean);
        return floatval($clean);
    }

    private function authorizeView(Proposal $proposal) {
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'financeiro'])) return;
        if ($proposal->user_id !== $user->id) abort(403);
    }

    private function authorizeEdit(Proposal $proposal) {
        $user = Auth::user();
        if ($user->role === 'comercial' && $proposal->user_id !== $user->id) abort(403);
        if (in_array($proposal->status, ['aprovada', 'cancelada', 'recusada'])) {
             if ($user->role === 'comercial') abort(403, 'Proposta finalizada.');
        }
    }
}