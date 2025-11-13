<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Base query: Se for admin vê tudo, se for comercial vê só o dele
        $query = Proposal::query();
        if ($user->role === 'comercial') {
            $query->where('user_id', $user->id);
        }

        // 1. Gráfico de Vendas (Últimos 6 meses)
        // Agrupado por mês
        $salesData = $query->clone()
            ->where('status', 'aprovada')
            ->where('approved_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(approved_at, "%Y-%m") as month'), 
                DB::raw('SUM(total_value) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Gráfico por Tipo de Serviço (Todo o período)
        $serviceData = $query->clone()
            ->where('status', 'aprovada')
            ->select('service_type', DB::raw('COUNT(*) as count'))
            ->groupBy('service_type')
            ->get();

        // 3. KPIs
        $totalProposals = $query->clone()->count();
        $approvedProposals = $query->clone()->where('status', 'aprovada')->count();
        
        // Taxa de Conversão
        $conversionRate = $totalProposals > 0 ? ($approvedProposals / $totalProposals) * 100 : 0;

        // Ticket Médio
        $totalValue = $query->clone()->where('status', 'aprovada')->sum('total_value');
        $ticketMedio = $approvedProposals > 0 ? ($totalValue / $approvedProposals) : 0;

        return view('stats.index', compact('salesData', 'serviceData', 'conversionRate', 'ticketMedio'));
    }
}