<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\RevenueTier;
use Illuminate\Http\Request;

class RevenueTierController extends Controller
{
    public function index()
    {
        // Ordenar pelo valor mínimo para ficar organizado na tela
        $tiers = RevenueTier::orderBy('min_value')->get();
        return view('settings.revenue-tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('settings.revenue-tiers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'required|numeric|gt:min_value', // Max deve ser maior que Min
        ]);

        RevenueTier::create($request->all());
        return redirect()->route('settings.revenue-tiers.index')->with('success', 'Meta criada com sucesso.');
    }

    // Note o nome da variável $revenueTier (singular) para bater com a rota
    public function edit(RevenueTier $revenueTier)
    {
        return view('settings.revenue-tiers.edit', compact('revenueTier'));
    }

    public function update(Request $request, RevenueTier $revenueTier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'required|numeric|gt:min_value',
        ]);

        $revenueTier->update($request->all());
        return redirect()->route('settings.revenue-tiers.index')->with('success', 'Meta atualizada com sucesso.');
    }

    public function destroy(RevenueTier $revenueTier)
    {
        $revenueTier->delete();
        return redirect()->route('settings.revenue-tiers.index')->with('success', 'Meta excluída com sucesso.');
    }
}