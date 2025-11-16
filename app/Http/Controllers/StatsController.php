<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Settings\RevenueTier;
use App\Models\Settings\Channel; // <-- IMPORTAR CANAIS
use App\Models\Settings\CommissionRule; // <-- IMPORTAR REGRAS
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Mostra o dashboard de resultados do vendedor.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // --- 1. BASE DE DADOS (Vendas Aprovadas no Mês) ---
        $myProposalsBase = Proposal::where('user_id', $user->id);
        
        $myApprovedThisMonthQuery = (clone $myProposalsBase)
            ->where('status', 'aprovada')
            ->whereBetween('approved_at', [$startOfMonth, $endOfMonth]);

        // --- 2. DADOS DOS CARDS ---
        $my_commissions = $myApprovedThisMonthQuery->sum('commission_value');
        $my_sales = $myApprovedThisMonthQuery->sum('total_value'); // Total de vendas (usado para metas)
        
        $my_pending = (clone $myProposalsBase)
            ->whereNotIn('status', ['aprovada', 'cancelada', 'recusada'])
            ->count();

        // --- 3. DADOS NOVOS SOLICITADOS ---
        
        // A. Lista de Propostas Aprovadas
        $myApprovedProposals = $myApprovedThisMonthQuery->with('client')->get();

        // B. Resumo de Serviços Vendidos
        $servicesSold = $myApprovedProposals->groupBy('service_type')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_value')
                ];
            });

        // C. Próxima Meta
        $nextTier = RevenueTier::where('min_value', '>', $my_sales)
                        ->orderBy('min_value', 'asc')
                        ->first();
        
        // D. Percentual de Comissão Atual (por Canal)
        $channels = Channel::with(['commissionRules.revenueTier'])->get();
        $currentPercentages = $channels->map(function ($channel) use ($my_sales) {
            
            // Tenta encontrar a regra exata para o faturamento atual
            $currentRule = $channel->commissionRules->first(function ($rule) use ($my_sales) {
                return $my_sales >= $rule->revenueTier->min_value && $my_sales <= $rule->revenueTier->max_value;
            });

            // Se o faturamento ultrapassou a meta máxima, pega a regra da meta mais alta
            if (!$currentRule) {
                $currentRule = $channel->commissionRules->sortByDesc('revenueTier.min_value')->first();
            }

            return [
                'channel_name' => $channel->name,
                'percentage' => $currentRule ? $currentRule->percentage : 0,
            ];
        });


        // --- 4. DADOS DOS GRÁFICOS (Performance ao longo do tempo) ---
        
        // Gráfico de Vendas Mensais (Últimos 12 meses)
        $monthlySalesQuery = (clone $myProposalsBase)
            ->where('status', 'aprovada')
            ->where('approved_at', '>=', Carbon::now()->subMonths(12))
            ->select(
                DB::raw('SUM(total_value) as total'),
                DB::raw("DATE_FORMAT(approved_at, '%Y-%m') as month_year")
            )
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->get();
        
        // --- TRADUÇÃO DOS MESES (NOVO) ---
        $monthlyLabels = $monthlySalesQuery->pluck('month_year')->map(function ($monthYear) {
            // Define o local e traduz (Ex: 2025-11 -> Nov/25)
            return Carbon::createFromFormat('Y-m', $monthYear)->locale('pt_BR')->translatedFormat('M/y');
        });
        
        $monthlySales = [
            'labels' => $monthlyLabels,
            'data' => $monthlySalesQuery->pluck('total'),
        ];

        // Gráfico de Vendas por Canal (ESTE MÊS) - (ATUALIZADO)
        $channelSalesQuery = (clone $myProposalsBase)
            ->where('status', 'aprovada')
            ->whereBetween('approved_at', [$startOfMonth, $endOfMonth]) // <-- MUDANÇA: de Anual para Mensal
            ->join('settings_channels', 'proposals.channel_id', '=', 'settings_channels.id')
            ->select(
                'settings_channels.name as channel_name',
                DB::raw('SUM(proposals.total_value) as total')
            )
            ->groupBy('settings_channels.name')
            ->get();
            
        $channelSales = [
            'labels' => $channelSalesQuery->pluck('channel_name'),
            'data' => $channelSalesQuery->pluck('total'),
        ];
        
        // --- 5. ENVIAR TUDO PARA A VIEW ---
        return view('stats.index', compact(
            'my_commissions',
            'my_sales',
            'my_pending',
            'myApprovedProposals', 
            'servicesSold',       
            'nextTier',           
            'currentPercentages', // <-- NOVO
            'monthlySales',
            'channelSales'
        ));
    }
}