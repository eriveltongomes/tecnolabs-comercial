<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-700">Olá, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-500">Aqui está o resumo das suas atividades.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                @if(in_array(Auth::user()->role, ['admin', 'financeiro']))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-gray-500 text-sm font-medium">Vendas Aprovadas (Este Mês)</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($data['total_sales'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="text-gray-500 text-sm font-medium">Comissões a Pagar (Este Mês)</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($data['total_commissions'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium">Propostas Pendentes</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">{{ $data['pending_count'] }}</div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-gray-500 text-sm font-medium">Minhas Comissões (Este Mês)</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">R$ {{ number_format($data['my_commissions'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-gray-500 text-sm font-medium">Total Vendido</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($data['my_sales'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-500">
                        <div class="text-gray-500 text-sm font-medium">Propostas em Aberto</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">{{ $data['my_pending'] }}</div>
                    </div>
                @endif

            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-bold mb-4">Últimas Propostas</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($data['recent_proposals'] as $prop)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ $prop->proposal_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $prop->client->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $prop->status === 'aprovada' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $prop->status === 'reprovada' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $prop->status === 'rascunho' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($prop->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">R$ {{ number_format($prop->total_value, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('proposals.show', $prop->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma movimentação recente.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="{{ route('proposals.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-bold">Ver todas as propostas &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>