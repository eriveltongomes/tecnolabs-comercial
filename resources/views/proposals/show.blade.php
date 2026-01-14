<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>{{ __('Detalhes da Proposta #') . $proposal->proposal_number }}</span>
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('proposals.pdf', $proposal->id) }}" target="_blank" class="px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    PDF
                </a>
                @if(!in_array($proposal->status, ['aprovada', 'cancelada', 'em_analise', 'recusada']))
                    <a href="{{ route('proposals.edit', $proposal->id) }}" class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Editar
                    </a>
                @endif
                <a href="{{ route('proposals.index') }}" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">Voltar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 p-4 rounded-lg flex justify-between items-center 
                {{ $proposal->status === 'aprovada' ? 'bg-green-100 text-green-800' : '' }}
                {{ $proposal->status === 'reprovada' ? 'bg-red-100 text-red-800' : '' }}
                {{ in_array($proposal->status, ['rascunho', 'aberta']) ? 'bg-blue-50 text-blue-800' : '' }}
                {{ $proposal->status === 'em_analise' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $proposal->status === 'cancelada' ? 'bg-gray-200 text-gray-600' : '' }}
                {{ $proposal->status === 'recusada' ? 'bg-red-200 text-red-900' : '' }}">
                
                <div>
                    <span class="font-bold text-lg">Status: {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                    @if(in_array($proposal->status, ['reprovada', 'recusada']) || ($proposal->status === 'cancelada' && $proposal->rejection_reason))
                        <div class="mt-1 text-sm font-bold text-red-700">Motivo: {{ $proposal->rejection_reason }}</div>
                    @endif
                    @if($proposal->status === 'aprovada')
                        <div class="mt-1 text-sm">Aprovado em: {{ $proposal->approved_at ? $proposal->approved_at->format('d/m/Y H:i') : '-' }}</div>
                    @endif
                </div>

                <div class="flex space-x-2">
                    @if(in_array($proposal->status, ['rascunho', 'aberta', 'reprovada']))
                        <form action="{{ route('proposals.sendToAnalysis', $proposal->id) }}" method="POST" onsubmit="return confirm('Cliente aprovou? Enviar para Financeiro?');">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Cliente Aceitou
                            </button>
                        </form>
                        
                        <button type="button" onclick="document.getElementById('refuseModal').style.display='block'" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 font-bold shadow flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Recusou
                        </button>
                    @endif

                    @if($proposal->status === 'em_analise' && in_array(Auth::user()->role, ['admin', 'financeiro']))
                        <button onclick="document.getElementById('rejectModal').style.display='block'" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold shadow flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Reprovar
                        </button>
                        <form action="{{ route('proposals.approve', $proposal->id) }}" method="POST" onsubmit="return confirm('Confirmar aprovação final?');">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Aprovar
                            </button>
                        </form>
                    @endif

                    @if(Auth::user()->role == 'admin')
                        @if($proposal->status === 'aprovada')
                            <button onclick="document.getElementById('reverseModal').style.display='block'" class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-900 font-bold shadow border border-red-900 ml-4 flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Estornar
                            </button>
                        @endif
                        <form action="{{ route('proposals.destroy', $proposal->id) }}" method="POST" class="ml-4" onsubmit="return confirm('DELETAR? Esta ação é permanente e NÃO PODE ser desfeita!');">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-900 font-bold shadow flex items-center" title="Deletar Proposta">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Deletar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b pb-6">
                        <div><h3 class="text-xs font-bold text-gray-400 uppercase mb-1">Cliente</h3><p class="text-lg font-bold">{{ $proposal->client->name }}</p></div>
                        <div><h3 class="text-xs font-bold text-gray-400 uppercase mb-1">Venda</h3><p class="text-sm text-gray-600">Vendedor: {{ $proposal->user->name }}<br>Canal: {{ $proposal->channel->name }}</p></div>
                        <div class="text-right"><h3 class="text-xs font-bold text-gray-400 uppercase mb-1">Total</h3><p class="text-3xl font-bold text-indigo-600">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</p></div>
                    </div>
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-indigo-500 pl-3">Escopo e Execução</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div class="bg-gray-50 p-4 rounded-lg"><h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Descrição</h4><p class="text-sm text-gray-700 whitespace-pre-line">{{ $proposal->scope_description }}</p></div>
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg"><h4 class="text-xs font-bold text-gray-500 uppercase mb-1">Local</h4><p class="text-sm font-bold">{{ $proposal->service_location }}</p></div>
                                <div class="bg-gray-50 p-4 rounded-lg"><h4 class="text-xs font-bold text-gray-500 uppercase mb-1">Data</h4><p class="text-sm font-bold">{{ $proposal->service_date->format('d/m/Y') }}</p></div>
                                <div class="bg-gray-50 p-4 rounded-lg"><h4 class="text-xs font-bold text-gray-500 uppercase mb-1">Pagamento</h4><p class="text-sm font-bold">{{ $proposal->payment_terms }}</p></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-l-4 border-green-500 pl-3">Detalhamento Financeiro (Visão Interna)</h3>
                        @php $labels = ['fuel' => 'Combustível', 'hotel' => 'Hospedagem', 'food' => 'Alimentação', 'other' => 'Outros']; @endphp
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalhes</th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Custo (Interno)</th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor Cobrado</th></tr></thead>
                                <tbody class="divide-y divide-gray-200">
                                    
                                    @php
                                        // --- CÁLCULO DE MARKUP GLOBAL (INTERNO) ---
                                        // Calcula um fator único que aplica a margem em TODOS os itens (Serviço, Instalação, Variáveis)
                                        // para que a coluna "Valor Cobrado" some exatamente o Total da Proposta.
                                        $baseMonthly = $proposal->service_details['monthly_cost'] ?? 0;
                                        $baseInstall = $proposal->service_details['installation_cost'] ?? 0;
                                        $months = $proposal->service_details['months'] ?? 1;
                                        $totalVars = $proposal->variableCosts->sum('cost');
                                        
                                        $totalBase = ($baseMonthly * $months) + $baseInstall + $totalVars;
                                        
                                        // Evita divisão por zero
                                        $globalFactor = ($totalBase > 0) ? ($proposal->total_value / $totalBase) : 1;
                                    @endphp

                                    @if($proposal->service_type != 'timelapse')
                                    <tr><td class="px-6 py-4 text-sm font-medium text-gray-500">Custos Indiretos</td><td class="px-6 py-4 text-sm text-gray-500">Rateio Custo Fixo ({{ $proposal->service_details['period_hours'] ?? 0 }}h)</td><td class="px-6 py-4 text-sm text-red-400 text-right">- R$ {{ number_format($costFixedProporcional, 2, ',', '.') }}</td><td class="px-6 py-4 text-sm text-gray-300 text-right">--</td></tr>
                                    <tr><td class="px-6 py-4 text-sm font-medium text-gray-500"></td><td class="px-6 py-4 text-sm text-gray-500">Amortização Cursos ({{ $proposal->service_details['period_hours'] ?? 0 }}h)</td><td class="px-6 py-4 text-sm text-red-400 text-right">- R$ {{ number_format($costCourseProporcional, 2, ',', '.') }}</td><td class="px-6 py-4 text-sm text-gray-300 text-right">--</td></tr>
                                    @php $equipId = $proposal->service_details['equipment_id'] ?? null; $equipName = $equipId ? \App\Models\Settings\Equipment::find($equipId)->name ?? 'Não encontrado' : 'Não selecionado'; @endphp
                                    <tr><td class="px-6 py-4 text-sm font-medium text-gray-900">Equipamento</td><td class="px-6 py-4 text-sm text-gray-500">{{ $equipName }} (Depreciação)</td><td class="px-6 py-4 text-sm text-red-400 text-right">- R$ {{ number_format($costEquipProporcional, 2, ',', '.') }}</td><td class="px-6 py-4 text-sm text-gray-300 text-right">--</td></tr>
                                    @endif

                                    @if($proposal->service_type == 'timelapse')
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Serviço Timelapse</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $months }} Meses</td>
                                            <td class="px-6 py-4 text-sm text-red-400 text-right">R$ {{ number_format($baseMonthly * $months, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">R$ {{ number_format(($baseMonthly * $months) * $globalFactor, 2, ',', '.') }}</td>
                                        </tr>
                                        @if($baseInstall > 0)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Instalação</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Taxa Única</td>
                                            <td class="px-6 py-4 text-sm text-red-400 text-right">R$ {{ number_format($baseInstall, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">R$ {{ number_format($baseInstall * $globalFactor, 2, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                    @else
                                        <tr><td class="px-6 py-4 text-sm font-medium text-gray-900">Mão de Obra</td><td class="px-6 py-4 text-sm text-gray-500">Piloto / Operador</td><td class="px-6 py-4 text-sm text-red-400 text-right">- R$ {{ number_format($proposal->service_details['labor_cost'] ?? 0, 2, ',', '.') }}</td><td class="px-6 py-4 text-sm text-gray-800 text-right"> (Incluso) </td></tr>
                                    @endif

                                    @foreach($proposal->variableCosts as $cost)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-500">Logística</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $labels[$cost->description] ?? $cost->description }}</td>
                                        <td class="px-6 py-4 text-sm text-red-400 text-right">- R$ {{ number_format($cost->cost, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-800 text-right">R$ {{ number_format($cost->cost * $globalFactor, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach

                                    @if($proposal->courtesy)
                                    <tr class="bg-blue-50"><td class="px-6 py-4 text-sm font-bold text-blue-800">CORTESIA</td><td class="px-6 py-4 text-sm text-blue-800" colspan="3">{{ $proposal->courtesy }}</td></tr>
                                    @endif
                                    
                                    <tr class="bg-gray-100 border-t-2 border-gray-300">
                                        <td class="px-6 py-4 text-base font-bold text-gray-900" colspan="3">VALOR FINAL DE VENDA</td>
                                        <td class="px-6 py-4 text-xl font-bold text-indigo-700 text-right">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(in_array(Auth::user()->role, ['admin', 'financeiro']))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <h4 class="text-xs font-bold text-yellow-800 uppercase tracking-wide mb-3">Auditoria Financeira (Visível para Admin/Financeiro)</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div><span class="text-sm text-gray-600 block">Imposto Aplicado</span><span class="text-xl font-bold text-gray-800">{{ $taxes_percent }}%</span></div>
                            <div><span class="text-sm text-gray-600 block">Margem de Lucro</span><span class="text-xl font-bold text-gray-800">{{ $proposal->profit_margin }}%</span></div>
                            <div><span class="text-sm text-gray-600 block">Comissão Gerada</span><span class="text-xl font-bold text-green-700">@if($proposal->commission_value) R$ {{ number_format($proposal->commission_value, 2, ',', '.') }} @else (Pendente) @endif</span></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="refuseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Motivo da Recusa (Cliente)</h3>
                <form action="{{ route('proposals.refuse', $proposal->id) }}" method="POST" class="mt-2 px-2 py-3">
                    @csrf @method('PATCH')
                    <label for="rejection_reason_refuse_show" class="text-sm text-gray-600 block text-left mb-2">Por que o cliente recusou esta proposta?</label>
                    <select name="rejection_reason" id="rejection_reason_refuse_show" class="w-full border-gray-300 rounded" required>
                        <option value="">Selecione um motivo...</option>
                        <option value="Preço Alto (Concorrência)">Preço Alto (Concorrência)</option>
                        <option value="Preço Alto (Orçamento Cliente)">Preço Alto (Orçamento Cliente)</option>
                        <option value="Prazo de Entrega">Prazo de Entrega</option>
                        <option value="Escopo Incompleto">Escopo Incompleto</option>
                        <option value="Projeto Adiado/Cancelado">Projeto Adiado/Cancelado (Cliente)</option>
                        <option value="Sem Resposta (Follow-up)">Sem Resposta (Follow-up)</option>
                        <option value="Outros">Outros</option>
                    </select>
                    <div class="flex justify-between mt-4">
                        <button type="button" onclick="document.getElementById('refuseModal').style.display='none'" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirmar Recusa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reprovar Proposta (Financeiro)</h3>
                <form action="{{ route('proposals.reject', $proposal->id) }}" method="POST" class="mt-2 px-2 py-3">
                    @csrf @method('PATCH')
                    <textarea name="rejection_reason" class="w-full border-gray-300 rounded" rows="3" placeholder="Motivo da reprovação interna..." required></textarea>
                    <div class="flex justify-between mt-4"><button type="button" onclick="document.getElementById('rejectModal').style.display='none'" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button><button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Confirmar</button></div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="reverseModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden overflow-y-auto h-full w-full" style="z-index: 60;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-xl rounded-md bg-white border-red-600">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-bold text-red-900">Estornar Venda Aprovada?</h3>
                <form action="{{ route('proposals.reverseApproval', $proposal->id) }}" method="POST" class="mt-2 px-2 py-3">
                    @csrf @method('PATCH')
                    <p class="text-sm text-gray-500 mb-2 text-left">Motivo do estorno (visível na auditoria):</p>
                    <textarea name="cancellation_reason" class="w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500" rows="3" required></textarea>
                    <div class="flex justify-between mt-4"><button type="button" onclick="document.getElementById('reverseModal').style.display='none'" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button><button type="submit" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Estornar</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>