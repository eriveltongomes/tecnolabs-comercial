<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0c-1.657 0-3-.895-3-2s1.343-2 3-2 3-.895 3-2-1.343-2-3-2m0 8c1.11 0 2.08-.402 2.599-1M12 16v1m0-1v-8m0 0c-1.11 0-2.08.402-2.599 1M12 16c-1.11 0-2.08-.402-2.599-1m5.198-3.799a4.978 4.978 0 01-1.07,1.07A4.978 4.978 0 0112 13a4.978 4.978 0 01-4.128-2.201 4.978 4.978 0 01-1.07-1.07A4.978 4.978 0 017 9c0-1.299.483-2.493 1.272-3.412a4.978 4.978 0 011.07-1.07A4.978 4.978 0 0112 3a4.978 4.978 0 014.128 2.201 4.978 4.978 0 011.07 1.07A4.978 4.978 0 0117 9c0 1.299-.483 2.493-1.272 3.412z"></path></svg>
            <span>
                Relatório de Rentabilidade (Lucro)
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Filtrar Período</h3>
                    <form action="{{ route('reports.profitability') }}" method="GET">
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Total Faturado</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($totalFaturado, 2, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Lucro Bruto Estimado</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">R$ {{ number_format($totalLucro, 2, ',', '.') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Margem Média</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($margemMedia, 2) }}%</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Rentabilidade por Serviço</h3>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Serviço</th>
                                    <th class="px-4 py-2 text-right">Faturado (R$)</th>
                                    <th class="px-4 py-2 text-right">Lucro (R$)</th>
                                    <th class="px-4 py-2 text-right">Margem (%)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($lucroPorServico as $servico => $data)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $serviceTranslations[$servico] ?? $servico }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">R$ {{ number_format($data['total_faturado'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-green-700 font-bold">R$ {{ number_format($data['total_lucro'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500 font-bold">{{ number_format($data['margem_media'], 2) }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Nenhum dado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Rentabilidade por Canal</h3>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Canal</th>
                                    <th class="px-4 py-2 text-right">Faturado (R$)</th>
                                    <th class="px-4 py-2 text-right">Lucro (R$)</th>
                                    <th class="px-4 py-2 text-right">Margem (%)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($lucroPorCanal as $canal => $data)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $canal }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">R$ {{ number_format($data['total_faturado'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-green-700 font-bold">R$ {{ number_format($data['total_lucro'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500 font-bold">{{ number_format($data['margem_media'], 2) }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Nenhum dado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>