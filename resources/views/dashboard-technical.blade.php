<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            👷 Painel Operacional
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Olá, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-500 text-sm">Aqui está o resumo das suas missões técnicas.</p>
                </div>
                <div class="text-right text-sm text-gray-400">
                    {{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('l, d \d\e F \d\e Y') }}
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Para Hoje</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $data['today_count'] }}</p>
                            <p class="text-xs text-indigo-500 font-medium mt-1">Ordens agendadas</p>
                        </div>
                        <div class="p-3 rounded-full bg-indigo-50 text-indigo-500">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Fila de Trabalho</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $data['pending_count'] }}</p>
                            <p class="text-xs text-yellow-600 font-medium mt-1">Pendentes Total</p>
                        </div>
                        <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Realizado (Mês)</p>
                            <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $data['completed_month'] }}</p>
                            <p class="text-xs text-green-600 font-medium mt-1">Missões concluídas</p>
                        </div>
                        <div class="p-3 rounded-full bg-green-50 text-green-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        🚀 Próximas Missões
                    </h3>
                    @if(count($data['next_missions']) > 0)
                        <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full font-bold">
                            Prioridade
                        </span>
                    @endif
                </div>

                @if(count($data['next_missions']) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data / Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço (Título)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($data['next_missions'] as $os)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 bg-gray-100 rounded p-1 mr-3 text-gray-500">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($os->scheduled_at)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($os->scheduled_at)->format('H:i') }}h
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $os->client->name ?? 'Cliente N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $os->client->city ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $os->title ?? 'Serviço Técnico' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusColors = [
                                                'pendente' => 'bg-gray-100 text-gray-800',
                                                'agendada' => 'bg-blue-100 text-blue-800',
                                                'em_execucao' => 'bg-yellow-100 text-yellow-800',
                                                'concluida' => 'bg-green-100 text-green-800',
                                                'cancelada' => 'bg-red-100 text-red-800',
                                            ];
                                            $labels = [
                                                'pendente' => 'Pendente',
                                                'agendada' => 'Agendada',
                                                'em_execucao' => 'Em Execução',
                                                'concluida' => 'Concluída',
                                                'cancelada' => 'Cancelada',
                                            ];
                                            $color = $statusColors[$os->status] ?? 'bg-gray-100 text-gray-800';
                                            $label = $labels[$os->status] ?? ucfirst($os->status);
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('work-orders.execute', $os->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold border border-indigo-200 px-4 py-1.5 rounded hover:bg-indigo-50 transition-colors inline-flex items-center gap-1">
                                            <span>Abrir OS</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="bg-gray-50 rounded-full h-24 w-24 flex items-center justify-center mx-auto mb-4">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Tudo limpo por aqui!</h3>
                        <p class="mt-1 text-gray-500">Você não possui missões pendentes na sua agenda.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>