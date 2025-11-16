<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>
                Relatório de Pagamento de Comissões
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Filtrar Período</h3>
                    <form action="{{ route('reports.commissions') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="start_date" value="Data Inicial" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" 
                                              :value="request('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString())" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Data Final" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" 
                                              :value="request('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString())" />
                            </div>
                            <div>
                                <x-input-label for="user_id" value="Vendedor (Todos)" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Todos os Vendedores</option>
                                    @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id }}" @selected(request('user_id') == $vendedor->id)>
                                            {{ $vendedor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <x-primary-button>Aplicar Filtro</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($groupedProposals->isEmpty())
                        <p class="text-center text-gray-500">Nenhuma comissão aprovada encontrada para este período.</p>
                    @else
                        
                        @foreach ($groupedProposals as $vendedorName => $proposals)
                            <div class="mb-8 p-4 border rounded-lg">
                                <h3 class="text-xl font-bold text-indigo-700 mb-4">{{ $vendedorName }}</h3>

                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Proposta</th>
                                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Aprovada em</th>
                                            <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Valor da Venda</th>
                                            <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Valor da Comissão</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($proposals as $proposal)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $proposal->proposal_number }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ $proposal->approved_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 text-right text-gray-700">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                            <td class="px-4 py-3 text-right text-green-600 font-bold">R$ {{ number_format($proposal->commission_value, 2, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                        
                                        <tr class="bg-gray-100 font-bold">
                                            <td colspan="3" class="px-4 py-3 text-right text-gray-800 uppercase">Total a Pagar ({{ $vendedorName }}):</td>
                                            <td class="px-4 py-3 text-right text-lg text-green-700">
                                                R$ {{ number_format($proposals->sum('commission_value'), 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endforeach

                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>