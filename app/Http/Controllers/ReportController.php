<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Relatório de Comissões para Pagamento.
     */
    public function commissions(Request $request)
    {
        $query = Proposal::with('user')
                        ->where('status', 'aprovada');
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

    /**
     * Relatório de Performance Gerencial.
     */
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

    /**
     * Relatório de Eficiência (Funil de Vendas).
     */
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

    /**
     * NOVO: Relatório de Rentabilidade (Lucro).
     */
    public function profitability(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $proposals = Proposal::with('channel')
                        ->where('status', 'aprovada')
                        ->whereBetween('approved_at', [$startDate, $endDate])
                        ->get();

        // Calcula o lucro bruto para cada proposta
        $proposalsComLucro = $proposals->map(function ($proposal) {
            $proposal->lucro_bruto = $proposal->total_value * ($proposal->profit_margin / 100);
            return $proposal;
        });

        // Cards Principais
        $totalFaturado = $proposalsComLucro->sum('total_value');
        $totalLucro = $proposalsComLucro->sum('lucro_bruto');
        $margemMedia = ($totalFaturado > 0) ? ($totalLucro / $totalFaturado) * 100 : 0;

        // Agrupa por Serviço
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
        
        // Agrupa por Canal
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

    /**
     * NOVO: Relatório de Clientes (Top Clientes).
     */
    public function clients(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfDay()->toDateString());

        $proposals = Proposal::with('client')
                        ->where('status', 'aprovada')
                        ->whereBetween('approved_at', [$startDate, $endDate])
                        ->get();

        // Agrupa por Cliente e calcula os totais
        $vendasPorCliente = $proposals->groupBy('client.name')
            ->map(function ($group) {
                return [
                    'client_id' => $group->first()->client_id,
                    'count' => $group->count(),
                    'total_value' => $group->sum('total_value'),
                    'ticket_medio' => $group->avg('total_value'),
                ];
            })
            ->sortByDesc('total_value'); // Ordena por quem mais faturou

        return view('reports.clients', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'vendasPorCliente' => $vendasPorCliente,
        ]);
    }
}