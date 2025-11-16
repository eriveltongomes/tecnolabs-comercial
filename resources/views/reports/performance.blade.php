<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6m6 0v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
            <span>
                Relatório de Performance Gerencial
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Filtrar Período</h3>
                    <form action="{{ route('reports.performance') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="start_date" value="Data Inicial" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" 
                                              :value="$startDate" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Data Final" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" 
                                              :value="$endDate" />
                            </div>
                            <div class="flex items-end">
                                <x-primary-button>Aplicar Filtro</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Total Faturado (Aprovado)</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($totalFaturado, 2, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">Total de Vendas</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $totalVendas }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Ticket Médio</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Vendas por Canal</h3>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Canal</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Vendas (#)</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Valor Total (R$)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vendasPorCanal as $canal => $data)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $canal }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ $data['count'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700 font-bold">R$ {{ number_format($data['total_value'], 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Nenhum dado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Vendas por Tipo de Serviço</h3>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Serviço</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Vendas (#)</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Valor Total (R$)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vendasPorServico as $servico => $data)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $serviceTranslations[$servico] ?? $servico }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ $data['count'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700 font-bold">R$ {{ number_format($data['total_value'], 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Nenhum dado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>