<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FixedCost;
use Illuminate\Http\Request;

class FixedCostController extends Controller
{
    public function index()
    {
        $fixedCosts = FixedCost::all();
        // Vamos passar o total para exibir na tela, ajuda na conferência
        $totalMonthly = $fixedCosts->sum('monthly_value');
        
        return view('settings.fixed-costs.index', compact('fixedCosts', 'totalMonthly'));
    }

    public function create()
    {
        return view('settings.fixed-costs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_value' => 'required|numeric|min:0',
        ]);

        FixedCost::create($request->all());

        return redirect()->route('settings.fixed-costs.index')->with('success', 'Custo Fixo cadastrado com sucesso.');
    }

    // Note: O parâmetro aqui deve ser $fixedCost (singular) conforme definimos no web.php
    public function edit(FixedCost $fixedCost)
    {
        return view('settings.fixed-costs.edit', compact('fixedCost'));
    }

    public function update(Request $request, FixedCost $fixedCost)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_value' => 'required|numeric|min:0',
        ]);

        $fixedCost->update($request->all());

        return redirect()->route('settings.fixed-costs.index')->with('success', 'Custo Fixo atualizado com sucesso.');
    }

    public function destroy(FixedCost $fixedCost)
    {
        $fixedCost->delete();
        return redirect()->route('settings.fixed-costs.index')->with('success', 'Custo Fixo excluído com sucesso.');
    }
}