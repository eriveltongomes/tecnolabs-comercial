<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\CommissionRule;
use App\Models\Settings\Channel;
use App\Models\Settings\RevenueTier;
use Illuminate\Http\Request;

class CommissionRuleController extends Controller
{
    public function index()
    {
        // Carregamos os relacionamentos para evitar queries extras (N+1 problem)
        $rules = CommissionRule::with(['channel', 'revenueTier'])
                    ->get()
                    ->sortBy('channel.name'); // Agrupa visualmente por canal
                    
        return view('settings.commission-rules.index', compact('rules'));
    }

    public function create()
    {
        // Precisamos enviar as listas para preencher os <select>
        $channels = Channel::all();
        $tiers = RevenueTier::orderBy('min_value')->get();
        
        return view('settings.commission-rules.create', compact('channels', 'tiers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'channel_id' => 'required|exists:settings_channels,id',
            'revenue_tier_id' => 'required|exists:settings_revenue_tiers,id',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Opcional: Verificar se já existe uma regra para esse par Canal+Meta
        $exists = CommissionRule::where('channel_id', $request->channel_id)
                    ->where('revenue_tier_id', $request->revenue_tier_id)
                    ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Já existe uma regra para este Canal e Meta. Edite a existente.']);
        }

        CommissionRule::create($request->all());
        return redirect()->route('settings.commission-rules.index')->with('success', 'Regra de Comissão criada com sucesso.');
    }

    public function edit(CommissionRule $commissionRule)
    {
        $channels = Channel::all();
        $tiers = RevenueTier::orderBy('min_value')->get();
        
        return view('settings.commission-rules.edit', compact('commissionRule', 'channels', 'tiers'));
    }

    public function update(Request $request, CommissionRule $commissionRule)
    {
        $request->validate([
            'channel_id' => 'required|exists:settings_channels,id',
            'revenue_tier_id' => 'required|exists:settings_revenue_tiers,id',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Verificação de duplicidade ignorando o próprio ID
        $exists = CommissionRule::where('channel_id', $request->channel_id)
                    ->where('revenue_tier_id', $request->revenue_tier_id)
                    ->where('id', '!=', $commissionRule->id)
                    ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Já existe uma regra para este Canal e Meta.']);
        }

        $commissionRule->update($request->all());
        return redirect()->route('settings.commission-rules.index')->with('success', 'Regra atualizada com sucesso.');
    }

    public function destroy(CommissionRule $commissionRule)
    {
        $commissionRule->delete();
        return redirect()->route('settings.commission-rules.index')->with('success', 'Regra excluída com sucesso.');
    }
}