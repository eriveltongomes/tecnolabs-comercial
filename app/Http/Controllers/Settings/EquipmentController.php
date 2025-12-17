<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
        return view('settings.equipment.index', compact('equipments'));
    }

    public function create()
    {
        return view('settings.equipment.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:drone,camera,acessorio,outros',
            'name' => 'required|string|max:255',
            'invested_value' => 'required|numeric|min:0', // <--- CORRIGIDO
            'lifespan_hours' => 'required|integer|min:1',
            'anac_registration' => 'nullable|string|max:255',
            'insurance_policy' => 'nullable|string|max:255',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date',
        ]);

        Equipment::create($data);
        return redirect()->route('settings.equipment.index')->with('success', 'Equipamento cadastrado com sucesso.');
    }

    public function edit(Equipment $equipment)
    {
        return view('settings.equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'type' => 'required|in:drone,camera,acessorio,outros',
            'name' => 'required|string|max:255',
            'invested_value' => 'required|numeric|min:0', 
            'lifespan_hours' => 'required|integer|min:1',
            'anac_registration' => 'nullable|string|max:255',
            'insurance_policy' => 'nullable|string|max:255',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_expiry' => 'nullable|date',
        ]);

        $equipment->update($data);
        return redirect()->route('settings.equipment.index')->with('success', 'Equipamento atualizado.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('settings.equipment.index')->with('success', 'Equipamento excluído.');
    }
}