<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
            {{ __('Diário de Bordo (Equipamentos)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-xs font-bold uppercase">Total de Equipamentos</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $data->count() }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-xs font-bold uppercase">Horas Voadas (Total)</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format($data->sum('total_hours'), 2, ',', '.') }} h</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-xs font-bold uppercase">Missões Realizadas</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $data->sum('total_missions') }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Atual</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Missões</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horas Totais</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vida Útil Consumida</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($data as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-sm text-gray-900">{{ $row->name }}</div>
                                        <div class="text-xs text-gray-500">ANAC: {{ $row->anac }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $row->status_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $row->status }}
                                        </span>
                                        @if($row->next_mission != '-')
                                            <div class="text-xs text-gray-500 mt-1">Próx: {{ $row->next_mission }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-700">
                                        {{ $row->total_missions }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-indigo-600">
                                        {{ number_format($row->total_hours, 2, ',', '.') }} h
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap align-middle">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min($row->usage_percent, 100) }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 text-right">
                                            {{ number_format($row->usage_percent, 1) }}% de {{ $row->lifespan }}h
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>