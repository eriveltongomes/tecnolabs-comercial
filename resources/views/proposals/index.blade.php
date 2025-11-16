<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>{{ __('Propostas Comerciais') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 text-right">
                        <a href="{{ route('proposals.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Nova Proposta
                        </a>
                    </div>
                    
                    @if(session('success'))<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>@endif
                    @if(session('info'))<div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">{{ session('info') }}</div>@endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($proposals as $proposal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $proposal->proposal_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $proposal->client->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($proposal->service_type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $proposal->status === 'aprovada' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $proposal->status === 'reprovada' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $proposal->status === 'rascunho' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $proposal->status === 'em_analise' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $proposal->status === 'cancelada' ? 'bg-gray-300 text-gray-600' : '' }}
                                            {{ $proposal->status === 'recusada' ? 'bg-red-200 text-red-900' : '' }}
                                            {{ $proposal->status === 'aberta' ? 'bg-blue-50 text-blue-600' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                                        </span>
                                        @if($proposal->status == 'recusada' && $proposal->rejection_reason)
                                            <span class="text-xs text-red-700 block italic" title="Motivo da Recusa">{{ $proposal->rejection_reason }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        
                                        <div class="flex justify-end items-center space-x-2">

                                            @if(in_array($proposal->status, ['rascunho', 'aberta', 'enviada', 'reprovada']))
                                                <form action="{{ route('proposals.sendToAnalysis', $proposal->id) }}" method="POST" onsubmit="return confirm('Cliente aprovou?');">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-900 font-bold" title="Cliente Aceitou (Enviar p/ Financeiro)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                </form>
                                                
                                                <button type="button" 
                                                        onclick="openRefuseModal('{{ route('proposals.refuse', $proposal->id) }}')" 
                                                        class="text-red-500 hover:text-red-800 font-bold" 
                                                        title="Cliente Recusou">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                            @endif

                                            <a href="{{ route('proposals.pdf', $proposal->id) }}" target="_blank" class="text-red-600 hover:text-red-900" title="Baixar PDF">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>

                                            @if(!in_array($proposal->status, ['aprovada', 'cancelada', 'em_analise', 'recusada']))
                                                <a href="{{ route('proposals.edit', $proposal->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('proposals.show', $proposal->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Ver Detalhes">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            
                                            @if(Auth::user()->role == 'admin')
                                                <form action="{{ route('proposals.destroy', $proposal->id) }}" method="POST" onsubmit="return confirm('DELETAR? Ação permanente!');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-800" title="Deletar Proposta">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Nenhuma proposta encontrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="refuseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Motivo da Recusa (Cliente)</h3>
                <form id="refuseModalForm" action="" method="POST" class="mt-2 px-2 py-3">
                    @csrf @method('PATCH')
                    <label for="rejection_reason_refuse_index" class="text-sm text-gray-600 block text-left mb-2">Por que o cliente recusou esta proposta?</label>
                    <select name="rejection_reason" id="rejection_reason_refuse_index" class="w-full border-gray-300 rounded" required>
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

    <script>
        function openRefuseModal(actionUrl) {
            // Pega o formulário do modal
            const form = document.getElementById('refuseModalForm');
            // Define a URL de ação correta para a proposta clicada
            form.action = actionUrl;
            // Mostra o modal
            document.getElementById('refuseModal').style.display = 'block';
        }
    </script>
</x-app-layout>