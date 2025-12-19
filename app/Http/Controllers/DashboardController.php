<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\RevenueTier; // Teto da Comissão (Individual)
use App\Models\MonthlyGoal; // Meta da Empresa (Global)
use App\Models\User;        // Para contar a equipe
use App\Models\WorkOrder;   // Model de Ordens de Serviço
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // =================================================================
        // 1. DASHBOARD TÉCNICO (OPERACIONAL)
        // =================================================================
        if ($user->role === 'tecnico') {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $data = [];

            // Query Base: Apenas OS onde o técnico é o responsável
            // CORREÇÃO AQUI: Mudamos de 'user_id' para 'technician_id'
            $myWorkOrders = WorkOrder::where('technician_id', $user->id);

            // Card 1: Agendados para Hoje (Não concluídos)
            $data['today_count'] = (clone $myWorkOrders)
                ->whereDate('scheduled_at', Carbon::today())
                ->where('status', '!=', 'concluida')
                ->where('status', '!=', 'cancelada')
                ->count();

            // Card 2: Pendências Gerais (Tudo que não foi feito ainda)
            $data['pending_count'] = (clone $myWorkOrders)
                ->whereIn('status', ['pendente', 'agendada', 'em_execucao'])
                ->count();

            // Card 3: Produtividade do Mês (Concluídas)
            $data['completed_month'] = (clone $myWorkOrders)
                ->where('status', 'concluida')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Lista: Próximas Missões (Ordenadas por data)
            $data['next_missions'] = (clone $myWorkOrders)
                ->whereIn('status', ['pendente', 'agendada', 'em_execucao'])
                ->orderBy('scheduled_at', 'asc')
                ->with('client') // Para mostrar o nome do cliente
                ->take(10)
                ->get();

            return view('dashboard-technical', compact('data'));
        }

        // =================================================================
        // 2. DASHBOARD PADRÃO (ADMIN / FINANCEIRO / COMERCIAL)
        // =================================================================
        
        $data = [];
        
        // --- DATAS ---
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysInMonth = $endOfMonth->daysInMonth;
        $currentDay = Carbon::now()->day;

        // --- CONFIGURAÇÃO DE METAS ---
        
        // A. Meta Individual (Teto da Comissão)
        $tetoIndividual = floatval(RevenueTier::max('max_value') ?? 0);

        // B. Meta Global da Empresa (Manual do Mês)
        $metaEmpresa = MonthlyGoal::where('month', $endOfMonth->month)
                                  ->where('year', $endOfMonth->year)
                                  ->value('amount');
        $metaEmpresa = floatval($metaEmpresa ?? 0);

        // C. Capacidade Instalada (Referência)
        $totalVendedores = User::count(); 
        $capacidadeEquipe = $tetoIndividual * $totalVendedores;

        // --- DEFINIÇÃO: QUAL META O DASHBOARD VAI PERSEGUIR? ---
        if (in_array($user->role, ['admin', 'financeiro'])) {
            // Admin persegue a Meta da Empresa
            $metaPrincipal = $metaEmpresa > 0 ? $metaEmpresa : $capacidadeEquipe;
            $metaLabel = "Meta da Empresa";
        } else {
            // Vendedor persegue sua Meta Individual (Teto da comissão)
            $metaPrincipal = $tetoIndividual;
            $metaLabel = "Sua Meta Mensal";
        }

        // --- CÁLCULO DE VENDAS (REALIZADO) ---
        $totalSales = 0;
        $salesQuery = Proposal::where('status', 'aprovada')
                              ->whereBetween('approved_at', [$startOfMonth, $endOfMonth]);

        if (in_array($user->role, ['admin', 'financeiro'])) {
            // --- VISÃO GLOBAL (Empresa) ---
            $totalSales = $salesQuery->sum('total_value');
            
            $data['total_sales'] = $totalSales;
            $data['total_commissions'] = Proposal::where('status', 'aprovada')
                                                 ->whereBetween('approved_at', [$startOfMonth, $endOfMonth])
                                                 ->sum('commission_value');
            
            $data['pending_count'] = Proposal::whereIn('status', ['em_analise'])->count();

            $data['recent_proposals'] = Proposal::with(['client', 'user'])
                                                ->latest()
                                                ->take(5)
                                                ->get();
        } else {
            // --- VISÃO INDIVIDUAL (Vendedor) ---
            $salesQuery->where('user_id', $user->id);
            $totalSales = $salesQuery->sum('total_value');

            $data['my_sales'] = $totalSales;
            $data['my_commissions'] = Proposal::where('user_id', $user->id)
                                              ->where('status', 'aprovada')
                                              ->whereBetween('approved_at', [$startOfMonth, $endOfMonth])
                                              ->sum('commission_value');

            $data['my_pending'] = Proposal::where('user_id', $user->id)
                                          ->whereIn('status', ['rascunho', 'aberta', 'em_analise'])
                                          ->count();
            
            $data['recent_proposals'] = Proposal::where('user_id', $user->id)
                                                ->with('client')
                                                ->latest()
                                                ->take(5)
                                                ->get();
        }

        // --- DADOS FINAIS PARA A VIEW (GRÁFICO E PROGRESSO) ---
        
        $percentage = ($metaPrincipal > 0) ? ($totalSales / $metaPrincipal) * 100 : 0;
        
        $data['goal_info'] = [
            'label' => $metaLabel,
            'target' => $metaPrincipal,
            'achieved' => $totalSales,
            'percentage' => $percentage,
            'remaining' => max(0, $metaPrincipal - $totalSales),
            'team_capacity' => $capacidadeEquipe 
        ];

        // Dados para o Gráfico de Ritmo (Burn-up)
        $dailySales = $salesQuery->get()->groupBy(function($date) {
            return Carbon::parse($date->approved_at)->day;
        });

        $labels = [];
        $dataIdeal = [];
        $dataActual = [];
        $cumulative = 0;
        
        $idealDaily = ($metaPrincipal > 0) ? ($metaPrincipal / $daysInMonth) : 0;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $i; 
            
            // Linha Ideal
            $dataIdeal[] = round($idealDaily * $i, 2);

            // Linha Real (Acumulativo) - Só preenche até o dia de hoje
            if ($i <= $currentDay) {
                $dayTotal = isset($dailySales[$i]) ? $dailySales[$i]->sum('total_value') : 0;
                $cumulative += $dayTotal;
                $dataActual[] = round($cumulative, 2);
            }
        }

        $data['chart'] = [
            'labels' => $labels,
            'ideal' => $dataIdeal,
            'actual' => $dataActual
        ];

        return view('dashboard', compact('data'));
    }
}