<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Settings\Equipment;
use App\Models\WorkOrder; // <--- NOVO IMPORT
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // --- RELATÓRIOS COMERCIAIS ---

    public function commissions(Request $request)
    {
        $query = Proposal::with('user')->where('status', 'aprovada');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $query->whereBetween('approved_at', [$startDate, $endDate]);
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $proposals = $query->orderBy('user_id')->orderBy('approved_at')->get();
        $groupedProposals = $proposals->groupBy('user.name');
        $vendedores = User::whereIn('role', ['comercial', 'admin'])->orderBy('name')->get();

        return view('reports.commissions', [
            'groupedProposals' => $groupedProposals,
            'vendedores' => $vendedores,
            'filters' => $request->all()
        ]);
    }

    public function performance(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $proposals = Proposal::with('channel')
                        ->where('status', 'aprovada')
                        ->whereBetween('approved_at', [$startDate, $endDate])
                        ->get();
                        
        $totalFaturado = $proposals->sum('total_value');
        $totalVendas = $proposals->count();
        $ticketMedio = ($totalVendas > 0) ? $totalFaturado / $totalVendas : 0;
        
        $vendasPorCanal = $proposals->groupBy('channel.name')
            ->map(function ($group) {
                return ['count' => $group->count(), 'total_value' => $group->sum('total_value')];
            })->sortByDesc('total_value');
            
        $vendasPorServico = $proposals->groupBy('service_type')
            ->map(function ($group) {
                return ['count' => $group->count(), 'total_value' => $group->sum('total_value')];
            })->sortByDesc('total_value');
            
        $serviceTranslations = ['drone' => 'Drone', 'timelapse' => 'Timelapse', 'tour_virtual' => 'Tour Virtual 360°'];

        return view('reports.performance', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalFaturado' => $totalFaturado,
            'totalVendas' => $totalVendas,
            'ticketMedio' => $ticketMedio,
            'vendasPorCanal' => $vendasPorCanal,
            'vendasPorServico' => $vendasPorServico,
            'serviceTranslations' => $serviceTranslations,
        ]);
    }

    public function efficiency(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $vendedores = [];
        
        if (in_array($user->role, ['admin', 'financeiro'])) {
            $vendedores = User::whereIn('role', ['comercial', 'admin'])->orderBy('name')->get();
        }
        
        $query = Proposal::query()->whereBetween('created_at', [$startDate, $endDate]);
        
        if (in_array($user->role, ['admin', 'financeiro'])) {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            $query->where('user_id', $user->id);
        }
        
        $created_count = $query->count();
        $approved_count = (clone $query)->where('status', 'aprovada')->count();
        $lost_count = (clone $query)->where('status', 'recusada')->count();
        $canceled_count = (clone $query)->where('status', 'cancelada')->count();
        $conversion_rate = ($created_count > 0) ? ($approved_count / $created_count) * 100 : 0;
        
        $loss_analysis = (clone $query)
            ->where('status', 'recusada')
            ->select('rejection_reason', DB::raw('count(*) as count'))
            ->groupBy('rejection_reason')
            ->orderByDesc('count')
            ->get();
            
        $total_lost_for_percent = $loss_analysis->sum('count');

        return view('reports.efficiency', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'vendedores' => $vendedores,
            'created_count' => $created_count,
            'approved_count' => $approved_count,
            'lost_count' => $lost_count,
            'canceled_count' => $canceled_count,
            'conversion_rate' => $conversion_rate,
            'loss_analysis' => $loss_analysis,
            'total_lost_for_percent' => $total_lost_for_percent,
        ]);
    }

    public function profitability(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $proposals = Proposal::with('channel')
                        ->where('status', 'aprovada')
                        ->whereBetween('approved_at', [$startDate, $endDate])
                        ->get();

        $proposalsComLucro = $proposals->map(function ($proposal) {
            $proposal->lucro_bruto = $proposal->total_value * ($proposal->profit_margin / 100);
            return $proposal;
        });

        $totalFaturado = $proposalsComLucro->sum('total_value');
        $totalLucro = $proposalsComLucro->sum('lucro_bruto');
        $margemMedia = ($totalFaturado > 0) ? ($totalLucro / $totalFaturado) * 100 : 0;

        $lucroPorServico = $proposalsComLucro->groupBy('service_type')
            ->map(function ($group) {
                $faturado = $group->sum('total_value');
                $lucro = $group->sum('lucro_bruto');
                return [
                    'count' => $group->count(),
                    'total_faturado' => $faturado,
                    'total_lucro' => $lucro,
                    'margem_media' => ($faturado > 0) ? ($lucro / $faturado) * 100 : 0,
                ];
            })->sortByDesc('total_lucro');
        
        $lucroPorCanal = $proposalsComLucro->groupBy('channel.name')
            ->map(function ($group) {
                $faturado = $group->sum('total_value');
                $lucro = $group->sum('lucro_bruto');
                return [
                    'count' => $group->count(),
                    'total_faturado' => $faturado,
                    'total_lucro' => $lucro,
                    'margem_media' => ($faturado > 0) ? ($lucro / $faturado) * 100 : 0,
                ];
            })->sortByDesc('total_lucro');

        $serviceTranslations = ['drone' => 'Drone', 'timelapse' => 'Timelapse', 'tour_virtual' => 'Tour Virtual 360°'];

        return view('reports.profitability', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalFaturado' => $totalFaturado,
            'totalLucro' => $totalLucro,
            'margemMedia' => $margemMedia,
            'lucroPorServico' => $lucroPorServico,
            'lucroPorCanal' => $lucroPorCanal,
            'serviceTranslations' => $serviceTranslations,
        ]);
    }

    public function clients(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfDay()->toDateString());

        $proposals = Proposal::with('client')
                        ->where('status', 'aprovada')
                        ->whereBetween('approved_at', [$startDate, $endDate])
                        ->get();

        $vendasPorCliente = $proposals->groupBy('client.name')
            ->map(function ($group) {
                return [
                    'client_id' => $group->first()->client_id,
                    'count' => $group->count(),
                    'total_value' => $group->sum('total_value'),
                    'ticket_medio' => $group->avg('total_value'),
                ];
            })
            ->sortByDesc('total_value');

        return view('reports.clients', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'vendasPorCliente' => $vendasPorCliente,
        ]);
    }

    // --- RELATÓRIOS OPERACIONAIS ---

    public function operationalEquipment()
    {
        $equipments = Equipment::with(['workOrders' => function($q) {
            $q->whereIn('status', ['concluida', 'em_execucao', 'agendada']);
        }])->get();

        $data = $equipments->map(function($eq) {
            $completedOps = $eq->workOrders->where('status', 'concluida');
            $totalSeconds = 0;
            foreach($completedOps as $os) {
                if ($os->started_at && $os->finished_at) {
                    $totalSeconds += $os->finished_at->diffInSeconds($os->started_at);
                }
            }
            $totalHours = round($totalSeconds / 3600, 2);
            $activeOs = $eq->workOrders->whereIn('status', ['em_execucao', 'agendada'])->first();
            
            return (object) [
                'name' => $eq->name,
                'anac' => $eq->anac_registration ?? 'N/A',
                'lifespan' => $eq->lifespan_hours,
                'total_missions' => $completedOps->count(),
                'total_hours' => $totalHours,
                'usage_percent' => $eq->lifespan_hours > 0 ? ($totalHours / $eq->lifespan_hours) * 100 : 0,
                'status' => $activeOs ? 'Em Uso / Agendado' : 'Disponível',
                'status_color' => $activeOs ? 'yellow' : 'green',
                'next_mission' => $activeOs ? $activeOs->scheduled_at->format('d/m H:i') : '-'
            ];
        });

        return view('reports.operational-equipment', compact('data'));
    }

    public function operationalProductivity(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $technicians = User::whereIn('role', ['admin', 'tecnico', 'comercial'])
            ->with(['workOrdersAsTechnician' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('scheduled_at', [$startDate, $endDate]);
            }])
            ->get();

        $data = $technicians->map(function($tech) {
            $total = $tech->workOrdersAsTechnician->count();
            $completed = $tech->workOrdersAsTechnician->where('status', 'concluida')->count();
            $canceled = $tech->workOrdersAsTechnician->where('status', 'cancelada')->count();
            $in_progress = $tech->workOrdersAsTechnician->where('status', 'em_execucao')->count();
            $lastActivity = $tech->workOrdersAsTechnician->sortByDesc('updated_at')->first();

            return (object) [
                'name' => $tech->name,
                'role' => ucfirst($tech->role),
                'total_assigned' => $total,
                'completed' => $completed,
                'canceled' => $canceled,
                'in_progress' => $in_progress,
                'last_activity' => $lastActivity ? $lastActivity->updated_at->format('d/m/Y H:i') : '-'
            ];
        })->sortByDesc('total_assigned');

        return view('reports.operational-productivity', [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * RELATÓRIO 3: MAPA DE STATUS (GARGALOS)
     * Mostra o funil operacional e onde as OSs estão paradas.
     */
    public function operationalStatus()
    {
        // Conta quantas OSs tem em cada status
        $stats = WorkOrder::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Garante que todos os status existam (mesmo zerados)
        $allStatuses = ['pendente', 'agendada', 'em_execucao', 'concluida', 'cancelada'];
        $data = [];
        $total = 0;

        foreach ($allStatuses as $status) {
            $count = $stats[$status] ?? 0;
            $data[$status] = $count;
            $total += $count;
        }

        // Calcula porcentagens
        $percentages = [];
        foreach ($data as $status => $count) {
            $percentages[$status] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }

        return view('reports.operational-status', compact('data', 'total', 'percentages'));
    }
}