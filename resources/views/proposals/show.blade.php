<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Proposta #') . $proposal->proposal_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('proposals.pdf', $proposal->id) }}" target="_blank" class="px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-300">
                    Gerar PDF
                </a>
                
                @if(!in_array($proposal->status, ['aprovada', 'cancelada', 'em_analise']))
                    <a href="{{ route('proposals.edit', $proposal->id) }}" class="px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600">
                        Editar
                    </a>
                @endif

                <a href="{{ route('proposals.index') }}" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700">
                    Voltar
                </a>
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
                {{ $proposal->status === 'cancelada' ? 'bg-gray-200 text-gray-600' : '' }}">
                
                <div>
                    <span class="font-bold text-lg">Status Atual: {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                    @if($proposal->status === 'reprovada')
                        <div class="mt-1 text-sm font-bold text-red-700">Motivo: {{ $proposal->rejection_reason }}</div>
                    @endif
                    @if($proposal->status === 'aprovada')
                        <div class="mt-1 text-sm">Aprovado em: {{ $proposal->approved_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>

                <div class="flex space-x-2">
                    
                    @if(in_array($proposal->status, ['rascunho', 'aberta', 'reprovada']))
                        <form action="{{ route('proposals.sendToAnalysis', $proposal->id) }}" method="POST" onsubmit="return confirm('O cliente aprovou? Enviar para análise do Financeiro?');">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow">
                                Cliente Aprovou (Enviar p/ Financeiro)
                            </button>
                        </form>
                    @endif

                    @if(in_array($proposal->status, ['em_analise']) && in_array(Auth::user()->role, ['admin', 'financeiro']))
                        <button onclick="document.getElementById('rejectModal').style.display='block'" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold shadow">
                            Reprovar
                        </button>

                        <form action="{{ route('proposals.approve', $proposal->id) }}" method="POST" onsubmit="return confirm('Confirmar aprovação final? Isso gera a comissão.');">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow">
                                Aprovar Proposta
                            </button>
                        </form>
                    @endif

                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-lg font-bold mb-2 text-gray-700">Dados do Cliente</h3>
                            <p><span class="text-gray-500">Cliente:</span> <strong class="text-lg">{{ $proposal->client->name }}</strong></p>
                            <p><span class="text-gray-500">Email:</span> {{ $proposal->client->email ?? '-' }}</p>
                            <p><span class="text-gray-500">Contato:</span> {{ $proposal->client->contact_name ?? '-' }}</p>
                            <p><span class="text-gray-500">CNPJ/CPF:</span> {{ $proposal->client->document ?? '-' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-2 text-gray-700">Dados da Venda</h3>
                            <p><span class="text-gray-500">Vendedor:</span> {{ $proposal->user->name }}</p>
                            <p><span class="text-gray-500">Canal:</span> {{ $proposal->channel->name }}</p>
                            <p><span class="text-gray-500">Serviço:</span> <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs font-bold uppercase">{{ $proposal->service_type }}</span></p>
                            <p><span class="text-gray-500">Data Criação:</span> {{ $proposal->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <h3 class="text-lg font-bold mb-4 text-gray-700">Detalhamento Financeiro</h3>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @if($proposal->service_type == 'drone' || $proposal->service_type == 'tour_virtual')
                                    <tr>
                                        <td class="px-6 py-4">Mão de Obra</td>
                                        <td class="px-6 py-4 text-right">R$ {{ number_format($proposal->service_details['labor_cost'] ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @elseif($proposal->service_type == 'timelapse')
                                    <tr>
                                        <td class="px-6 py-4">Mensalidade ({{ $proposal->service_details['months'] ?? 1 }} meses)</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-xs text-gray-500">R$ {{ number_format($proposal->service_details['monthly_cost'] ?? 0, 2, ',', '.') }} / mês</div>
                                            R$ {{ number_format(($proposal->service_details['monthly_cost'] ?? 0) * ($proposal->service_details['months'] ?? 1), 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @if(!empty($proposal->service_details['installation_cost']))
                                    <tr>
                                        <td class="px-6 py-4">Taxa de Instalação</td>
                                        <td class="px-6 py-4 text-right">R$ {{ number_format($proposal->service_details['installation_cost'], 2, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                @endif

                                @foreach($proposal->variableCosts as $cost)
                                <tr>
                                    <td class="px-6 py-4 text-gray-600">Logística: {{ $cost->description }}</td>
                                    <td class="px-6 py-4 text-right text-gray-600">R$ {{ number_format($cost->cost, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                
                                <tr class="bg-gray-100">
                                    <td class="px-6 py-4 font-bold text-gray-900 text-lg">VALOR TOTAL DA PROPOSTA</td>
                                    <td class="px-6 py-4 text-right font-bold text-indigo-700 text-xl">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                </tr>

                                <tr class="bg-yellow-50">
                                    <td class="px-6 py-4 text-sm text-yellow-800">Comissão Prevista (Vendedor)</td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-yellow-700">
                                        @if($proposal->commission_value)
                                            R$ {{ number_format($proposal->commission_value, 2, ',', '.') }}
                                        @else
                                            <span class="text-xs font-normal">(Será gerada na aprovação)</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reprovar Proposta</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">Informe o motivo para devolver ao vendedor.</p>
                    <form action="{{ route('proposals.reject', $proposal->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <textarea name="rejection_reason" class="w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Motivo..." required></textarea>
                        <div class="mt-4 flex justify-between">
                            <button type="button" onclick="document.getElementById('rejectModal').style.display='none'" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancelar</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>