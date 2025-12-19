<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\MonthlyGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyGoalController extends Controller
{
    public function index()
    {
        // Lista as metas ordenadas da mais recente para a antiga
        $goals = MonthlyGoal::orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->get();

        return view('settings.monthly-goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        // 1. TRATAMENTO DE VALOR (R$ -> Float)
        // O input vem como "100.000,00". Precisamos limpar para salvar.
        $amount = $request->amount;
        if($amount) {
            $amount = str_replace('.', '', $amount); // Remove pontos de milhar
            $amount = str_replace(',', '.', $amount); // Troca vírgula por ponto
            // Atualiza o request com o valor limpo para passar na validação
            $request->merge(['amount' => $amount]);
        }

        // 2. VALIDAÇÃO
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'amount' => 'required|numeric|min:0',
        ]);

        // 3. SALVAR / ATUALIZAR
        MonthlyGoal::updateOrCreate(
            [
                'month' => $request->month,
                'year' => $request->year,
            ],
            [
                'amount' => $request->amount, // Já está limpo aqui
                'user_id' => Auth::id()
            ]
        );

        return redirect()->back()->with('success', 'Meta configurada com sucesso!');
    }

    public function destroy($id)
    {
        $goal = MonthlyGoal::findOrFail($id);
        $goal->delete();

        return redirect()->back()->with('success', 'Meta removida com sucesso!');
    }
}