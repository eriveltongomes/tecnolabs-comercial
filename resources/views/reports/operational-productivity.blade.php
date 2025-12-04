<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            {{ __('Relatório de Produtividade (Técnica)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <form method="GET" action="{{ route('reports.operationalProductivity') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Filtrar
                    </button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profissional</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">OS Atribuídas</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Em Andamento</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Concluídas</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Canceladas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Última Atividade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($data as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-sm text-gray-900">{{ $row->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $row->role }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900 font-bold">
                                        {{ $row->total_assigned }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($row->in_progress > 0)
                                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">{{ $row->in_progress }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-green-600 font-bold">{{ $row->completed }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-red-500">
                                        {{ $row->canceled > 0 ? $row->canceled : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $row->last_activity }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum registro encontrado no período.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>