<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::all();
        return view('settings.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('settings.taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Checkbox retorna 'on' ou null. Convertemos para booleano.
        $data = $request->all();
        $data['is_default'] = $request->has('is_default');

        Tax::create($data);

        return redirect()->route('settings.taxes.index')->with('success', 'Imposto cadastrado com sucesso.');
    }

    public function edit(Tax $tax)
    {
        return view('settings.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data = $request->all();
        $data['is_default'] = $request->has('is_default');

        $tax->update($data);

        return redirect()->route('settings.taxes.index')->with('success', 'Imposto atualizado com sucesso.');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return redirect()->route('settings.taxes.index')->with('success', 'Imposto excluído com sucesso.');
    }
}