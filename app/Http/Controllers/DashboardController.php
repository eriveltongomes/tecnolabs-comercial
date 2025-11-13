<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];
        
        // Definir o início e fim do mês atual
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        if (in_array($user->role, ['admin', 'financeiro'])) {
            // --- VISÃO FINANCEIRO (GLOBAL - MÊS ATUAL) ---
            
            $data['total_sales'] = Proposal::where('status', 'aprovada')
                                    ->whereBetween('approved_at', [$startOfMonth, $endOfMonth])
                                    ->sum('total_value');

            $data['total_commissions'] = Proposal::where('status', 'aprovada')
                                            ->whereBetween('approved_at', [$startOfMonth, $endOfMonth])
                                            ->sum('commission_value');

            // Pendentes é acumulativo (tudo que está parado, independente do mês)
            $data['pending_count'] = Proposal::whereIn('status', ['em_analise'])->count();

            $data['recent_proposals'] = Proposal::with(['client', 'user'])
                                        ->latest()
                                        ->take(5)
                                        ->get();

        } else {
            // --- VISÃO COMERCIAL (PESSOAL - MÊS ATUAL) ---

            $data['my_commissions'] = Proposal::where('user_id', $user->id)
                                        ->where('status', 'aprovada')
                                        ->whereBetween('approved_at', [$startOfMonth, $endOfMonth])
                                        ->sum('commission_value');

            $data['my_sales'] = Proposal::where('user_id', $user->id)
                                    ->where('status', 'aprovada')
                                    ->whereBetween('approved_at', [$startOfMonth, $endOfMonth])
                                    ->sum('total_value');

            // Propostas em aberto (Acumulativo)
            $data['my_pending'] = Proposal::where('user_id', $user->id)
                                    ->whereIn('status', ['rascunho', 'aberta', 'em_analise'])
                                    ->count();
            
            $data['recent_proposals'] = Proposal::where('user_id', $user->id)
                                        ->with('client')
                                        ->latest()
                                        ->take(5)
                                        ->get();
        }

        return view('dashboard', compact('data'));
    }
}