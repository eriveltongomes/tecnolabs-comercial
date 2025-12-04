<?php

namespace App\Http\Controllers;

use App\Models\ChecklistModel;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ChecklistModelController extends Controller
{
    public function index()
    {
        $models = ChecklistModel::withCount('items')->latest()->get();
        return view('checklist-models.index', compact('models'));
    }

    public function create()
    {
        return view('checklist-models.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:aro,pre_voo,pos_voo,instalacao',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.text' => 'required|string|max:255',
        ]);

        $model = ChecklistModel::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'description' => $data['description'],
            'is_active' => true
        ]);

        foreach ($data['items'] as $index => $item) {
            $isCritical = isset($item['is_critical']) && ($item['is_critical'] == 'on' || $item['is_critical'] == '1' || $item['is_critical'] === true);

            $model->items()->create([
                'text' => $item['text'],
                'help_text' => $item['help_text'] ?? null,
                'is_critical' => $isCritical,
                'order' => $index,
                'probability' => $item['probability'] ?? null,
                'severity' => $item['severity'] ?? null,
                'risk_level' => $item['risk_level'] ?? null,
                'tolerability' => $item['tolerability'] ?? null,
                'mitigation' => $item['mitigation'] ?? null,
            ]);
        }

        return redirect()->route('checklist-models.index')->with('success', 'Modelo criado com sucesso!');
    }

    public function edit(ChecklistModel $checklistModel)
    {
        // O Laravel automaticamente filtra os deletados (soft deleted) aqui
        $checklistModel->load(['items' => function($q) {
            $q->orderBy('order');
        }]);
        
        return view('checklist-models.edit', compact('checklistModel'));
    }

    public function update(Request $request, ChecklistModel $checklistModel)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:aro,pre_voo,pos_voo,instalacao',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
        ]);

        $checklistModel->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'description' => $data['description'],
        ]);

        // 1. Identifica IDs que devem permanecer
        $itemsToKeep = [];
        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                if (isset($item['id']) && $item['id']) {
                    $itemsToKeep[] = $item['id'];
                }
            }
        }

        // 2. Executa o Soft Delete nos removidos
        // AGORA SEM TRY/CATCH, pois o SoftDelete não viola FK
        $checklistModel->items()->whereNotIn('id', $itemsToKeep)->delete();

        // 3. Atualiza ou Cria
        if (!empty($request->items)) {
            foreach ($request->items as $index => $item) {
                $isCritical = isset($item['is_critical']) && ($item['is_critical'] == 'on' || $item['is_critical'] == '1' || $item['is_critical'] === true);

                $checklistModel->items()->updateOrCreate(
                    ['id' => $item['id'] ?? null], 
                    [
                        'checklist_model_id' => $checklistModel->id,
                        'text' => $item['text'],
                        'help_text' => $item['help_text'] ?? null,
                        'is_critical' => $isCritical,
                        'order' => $index,
                        'probability' => $item['probability'] ?? null,
                        'severity' => $item['severity'] ?? null,
                        'risk_level' => $item['risk_level'] ?? null,
                        'tolerability' => $item['tolerability'] ?? null,
                        'mitigation' => $item['mitigation'] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('checklist-models.index')->with('success', 'Modelo atualizado!');
    }

    public function destroy(ChecklistModel $checklistModel)
    {
        try {
            // Aqui também seria ideal soft delete no modelo pai, mas vamos manter simples por enquanto
            $checklistModel->delete();
            return redirect()->route('checklist-models.index')->with('success', 'Modelo excluído.');
        } catch (QueryException $e) {
            return redirect()->route('checklist-models.index')->with('error', 'Não é possível excluir este modelo pois ele já foi utilizado.');
        }
    }
}