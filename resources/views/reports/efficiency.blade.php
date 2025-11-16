<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            <span>
                Relatório de Eficiência (Funil de Vendas)
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Filtrar Período</h3>
                    <form action="{{ route('reports.efficiency') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="start_date" value="Data Inicial (Criação)" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" 
                                              :value="$startDate" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="Data Final (Criação)" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" 
                                              :value="$endDate" />
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

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm font-medium">Criadas</div>
                    <div class="text-4xl font-bold text-gray-800 mt-2">{{ $created_count }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm font-medium">Aprovadas</div>
                    <div class="text-4xl font-bold text-green-600 mt-2">{{ $approved_count }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm font-medium">Perdidas (Cliente)</div>
                    <div class="text-4xl font-bold text-red-600 mt-2">{{ $lost_count }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-gray-500 text-sm font-medium">Canceladas (Interno)</div>
                    <div class="text-4xl font-bold text-gray-500 mt-2">{{ $canceled_count }}</div>
                </div>
                <div class="bg-indigo-600 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-indigo-100 text-sm font-medium">Taxa de Conversão</div>
                    <div class="text-4xl font-bold text-white mt-2">{{ number_format($conversion_rate, 1) }}%</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Análise de Perdas (Propostas Recusadas pelo Cliente)</h3>
                    
                    @if($loss_analysis->isEmpty())
                        <p class="text-center text-gray-500">Nenhuma proposta recusada no período para analisar.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Motivo da Recusa</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Quantidade (#)</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Percentual (%)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($loss_analysis as $motivo)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $motivo->rejection_reason ?? 'Não especificado' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-red-600 font-bold text-base">{{ $motivo->count }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">
                                        {{ number_format(($motivo->count / $total_lost_for_percent) * 100, 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td class="px-4 py-3 text-left">Total de Perdas Analisadas</td>
                                    <td class="px-4 py-3 text-right text-lg text-red-700">{{ $total_lost_for_percent }}</td>
                                    <td class="px-4 py-3 text-right">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>