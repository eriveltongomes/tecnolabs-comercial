<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-2.87l-1.391 1.392A4.982 4.982 0 0113 18H5a2 2 0 01-2-2V8a2 2 0 012-2h2.586a1 1 0 01.707.293l7 7a1 1 0 01.293.707V15a5 5 0 01-5 5H5a2 2 0 01-2-2v-1.172a2 2 0 01.586-1.414l5.828-5.828A2 2 0 0110.586 5H13l2.828 2.828a1 1 0 01.293.707v3.172a1 1 0 01-1 1H11m0 0l-1 1m0 0l1 1m-1-1v-2m1 2h-2"></path></svg>
            <span>
                Relatório de Clientes (Curva ABC)
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Filtrar Período</h3>
                    <form action="{{ route('reports.clients') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="start_date" value="Data Inicial (Aprovação)" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" 
                                              :value="$startDate" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Data Final (Aprovação)" />
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Top Clientes por Faturamento</h3>
                    @php $totalAcumulado = 0; $faturamentoTotalPeriodo = $vendasPorCliente->sum('total_value'); @endphp
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Ranking</th>
                                    <th class="px-4 py-2 text-left">Cliente</th>
                                    <th class="px-4 py-2 text-right">Vendas (#)</th>
                                    <th class="px-4 py-2 text-right">Ticket Médio (R$)</th>
                                    <th class="px-4 py-2 text-right">Faturado (R$)</th>
                                    <th class="px-4 py-2 text-right">Rep. (%)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vendasPorCliente as $clienteNome => $data)
                                    @php $totalAcumulado += $data['total_value']; @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-bold text-gray-700">{{ $loop->iteration }}º</td>
                                        <td class="px-4 py-3 font-medium text-indigo-600">
                                            <a href="{{ route('clients.edit', $data['client_id']) }}" title="Ver Cliente">
                                                {{ $clienteNome }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-500">{{ $data['count'] }}</td>
                                        <td class="px-4 py-3 text-right text-gray-500">R$ {{ number_format($data['ticket_medio'], 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-gray-800 font-bold">R$ {{ number_format($data['total_value'], 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-gray-500 font-medium">
                                            {{ number_format(($data['total_value'] / $faturamentoTotalPeriodo) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @empty
                                <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">Nenhuma venda aprovada no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>