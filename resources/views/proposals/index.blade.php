<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Propostas Comerciais') }}</h2>
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
                                            {{ $proposal->status === 'enviada' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $proposal->status === 'aberta' ? 'bg-blue-50 text-blue-600' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        
                                        @if(in_array($proposal->status, ['rascunho', 'aberta', 'enviada', 'reprovada']))
                                            <form action="{{ route('proposals.sendToAnalysis', $proposal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('O Cliente aprovou? Enviar para Financeiro?');">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2 font-bold" title="Cliente Aceitou">
                                                    Aceitou
                                                </button>
                                            </form>

                                            <form action="{{ route('proposals.refuse', $proposal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('O Cliente recusou a proposta? Isso fechará a negociação.');">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-red-500 hover:text-red-800 mr-3 font-bold" title="Cliente Recusou">
                                                    Recusou
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('proposals.pdf', $proposal->id) }}" target="_blank" class="text-red-600 hover:text-red-900 mr-3 font-bold" title="Baixar PDF">PDF</a>

                                        @if(!in_array($proposal->status, ['aprovada', 'cancelada', 'em_analise', 'recusada']))
                                            <a href="{{ route('proposals.edit', $proposal->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3 font-bold">Editar</a>
                                        @endif
                                        
                                        <a href="{{ route('proposals.show', $proposal->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Ver</a>
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
</x-app-layout>