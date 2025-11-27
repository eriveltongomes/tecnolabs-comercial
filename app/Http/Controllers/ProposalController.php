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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filtro de Permissão
        if (in_array($user->role, ['admin', 'financeiro'])) {
            $query = Proposal::with(['client', 'user', 'channel']);
        } else {
            $query = Proposal::with(['client', 'channel'])->where('user_id', $user->id);
        }

        // Filtro de Status (se vier na URL)
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
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

    // --- SALVAR (STORE) ---
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'channel_id' => 'required|exists:settings_channels,id',
            'service_type' => 'required|in:drone,timelapse,tour_virtual',
            'profit_margin' => 'required', // Aceita texto formatado, limpamos depois
            'details' => 'nullable|array',
            'variable_costs' => 'nullable|array',
            'total_value' => 'required',
            
            // Campos do PDF
            'service_location' => 'required|string|max:255',
            'service_date' => 'required|date',
            'payment_terms' => 'required|string|max:500',
            'courtesy' => 'nullable|string|max:255',
            'scope_description' => 'required|string|max:5000',
        ]);

        $proposal = new Proposal();
        
        // --- CORREÇÃO DA NUMERAÇÃO (FIX) ---
        // Busca o último número do ano para somar +1 (evita erro de duplicidade se apagar)
        $year = date('Y');
        $lastProposal = Proposal::where('proposal_number', 'like', $year . '-%')
                                ->orderBy('id', 'desc')
                                ->first();
        
        if ($lastProposal) {
            // Pega o número depois do traço (ex: 2025-0007 -> pega 7)
            $lastNumber = intval(substr($lastProposal->proposal_number, 5));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $proposal->proposal_number = $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT); 
        // ------------------------------------

        $proposal->user_id = Auth::id();
        $proposal->client_id = $data['client_id'];
        $proposal->channel_id = $data['channel_id'];
        $proposal->service_type = $data['service_type'];
        
        // Campos do PDF
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
        $proposal->profit_margin = $this->parseMoney($data['profit_margin']);
        
        $proposal->status = 'rascunho';
        $proposal->save();

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

    // --- VISUALIZAÇÃO (SHOW) ---
    public function show(Proposal $proposal)
    {
        $this->authorizeView($proposal);
        
        // Lógica apenas para exibição de custos (Rateio visual)
        $totalMonthlyFixed = FixedCost::sum('monthly_value');
        $fixedCostPerHour = $totalMonthlyFixed > 0 ? ($totalMonthlyFixed / 192) : 0;
        
        $details = $proposal->service_details;
        $hours = floatval($details['period_hours'] ?? 0);
        
        $costFixedProporcional = 0;
        $costEquipProporcional = 0;
        $costCourseProporcional = 0;

        // Cursos
        $courses = Course::all();
        foreach($courses as $cs) {
            if ($cs->lifespan_hours > 0) $costCourseProporcional += ($cs->invested_value / $cs->lifespan_hours);
        }
        $costCourseProporcional = $costCourseProporcional * $hours;

        // Equipamentos e Fixos (Apenas se for hora técnica)
        if ($proposal->service_type != 'timelapse') {
            $costFixedProporcional = $fixedCostPerHour * $hours;
            if (!empty($details['equipment_id'])) {
                $equipment = Equipment::find($details['equipment_id']);
                if ($equipment && $equipment->lifespan_hours > 0) {
                    $hourlyDepreciation = $equipment->invested_value / $equipment->lifespan_hours;
                    $costEquipProporcional = $hourlyDepreciation * $hours;
                }
            }
        }
        
        $taxes_percent = Tax::where('is_default', true)->sum('percentage');

        return view('proposals.show', compact('proposal', 'costFixedProporcional', 'costEquipProporcional', 'costCourseProporcional', 'taxes_percent'));
    }

    // --- EDIÇÃO (EDIT) ---
    public function edit(Proposal $proposal)
    {
        $this->authorizeEdit($proposal);
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'financeiro'])) { $clients = Client::all(); } 
        else { $clients = Client::where('created_by_user_id', $user->id)->get(); }
        $channels = Channel::all();
        $equipments = Equipment::all();
        $variableCosts = [];
        foreach($proposal->variableCosts as $vc) { $variableCosts[$vc->description] = $vc->cost; }
        return view('proposals.edit', compact('proposal', 'clients', 'channels', 'variableCosts', 'equipments'));
    }

    // --- ATUALIZAÇÃO (UPDATE) ---
    public function update(Request $request, Proposal $proposal)
    {
        $this->authorizeEdit($proposal);

        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'channel_id' => 'required|exists:settings_channels,id',
            'service_type' => 'required|in:drone,timelapse,tour_virtual',
            'status' => 'required|in:rascunho,aberta,em_analise,cancelada',
            'profit_margin' => 'required', // Valida
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
        $proposal->profit_margin = $this->parseMoney($data['profit_margin']);
        
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

    // --- CALCULADORA (AJAX) ---
    public function calculate(Request $request)
    {
        $serviceType = $request->input('service_type');
        $details = $request->input('details', []);
        $vars = $request->input('variable_costs', []);
        $channelId = $request->input('channel_id');
        $profitMargin = $this->parseMoney($request->input('profit_margin', 0));

        $totalVariable = 0;
        foreach ($vars as $cost) { $totalVariable += $this->parseMoney($cost); }

        $totalMonthlyFixed = FixedCost::sum('monthly_value');
        $fixedCostPerHour = $totalMonthlyFixed > 0 ? ($totalMonthlyFixed / 192) : 0;

        $equipDepreciationHour = 0;
        if (!empty($details['equipment_id'])) {
            $equipment = Equipment::find($details['equipment_id']);
            if ($equipment && $equipment->lifespan_hours > 0) {
                $equipDepreciationHour = ($equipment->invested_value / $equipment->lifespan_hours);
            }
        }

        $courses = Course::all();
        $courseDepreciationHour = 0;
        foreach($courses as $cs) {
            if ($cs->lifespan_hours > 0) $courseDepreciationHour += ($cs->invested_value / $cs->lifespan_hours);
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

    // --- AÇÕES DO FLUXO ---
    public function generatePdf(Proposal $proposal) {
        $this->authorizeView($proposal);
        $pdf = Pdf::loadView('proposals.pdf', compact('proposal'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Proposta_' . $proposal->proposal_number . '.pdf');
    }

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

    public function refuse(Request $request, Proposal $proposal) { 
        $this->authorizeEdit($proposal);
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $proposal->update([
            'status' => 'recusada', 
            'rejection_reason' => $request->rejection_reason 
        ]); 
        
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Cliente recusou');
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
        
        if (function_exists('activity')) activity()->performedOn($proposal)->log('Aprovou (Comissão: R$ ' . $commissionValue . ')');
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

        if (function_exists('activity')) activity()->performedOn($proposal)->withProperties(['motivo' => $request->cancellation_reason])->log('REALIZOU ESTORNO');
        return redirect()->back()->with('success', 'Venda estornada com sucesso!');
    }

    public function destroy(Proposal $proposal) {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        if (function_exists('activity')) activity()->performedOn($proposal)->log('DELETOU a proposta permanentemente');
        $proposal->delete();
        return redirect()->route('proposals.index')->with('success', 'Proposta deletada permanentemente.');
    }

    // --- MÉTODOS AUXILIARES ---
    private function getCommissionPercentageBasedOnHistory($userId, $channelId, $currentValue) {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlySales = Proposal::where('user_id', $userId)->where('status', 'aprovada')->whereBetween('approved_at', [$startOfMonth, $endOfMonth])->sum('total_value');
        $projectedTotal = $monthlySales + $currentValue;
        $commissionRule = CommissionRule::where('channel_id', $channelId)->whereHas('revenueTier', function($q) use ($projectedTotal) { $q->where('min_value', '<=', $projectedTotal)->where('max_value', '>=', $projectedTotal); })->first();
        if (!$commissionRule) $commissionRule = CommissionRule::where('channel_id', $channelId)->orderByDesc('percentage')->first();
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
        if ($user->role === 'admin') return; 
        if ($user->role === 'comercial' && $proposal->user_id !== $user->id) abort(403); 
        // Bloqueia edição se finalizada
        if (in_array($proposal->status, ['aprovada', 'cancelada', 'recusada'])) { 
            if ($user->role === 'comercial') abort(403, 'Proposta finalizada.'); 
        } 
    }
}