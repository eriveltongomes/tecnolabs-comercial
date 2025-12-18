<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4 md:mb-0">
                {{ __('Detalhes da Ordem de Serviço') }} <span class="text-indigo-600">#{{ $workOrder->id }}</span>
            </h2>
            
            <div class="flex space-x-3">
                <a href="{{ route('work-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Voltar
                </a>

                <a href="{{ route('work-orders.pdf', $workOrder->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    🖨️ Gerar PDF
                </a>

                @if(Auth::user()->role === 'admin')
                <a href="{{ route('work-orders.edit', $workOrder->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    ✏️ Editar
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $workOrder->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Cliente: <span class="font-medium text-gray-800">{{ $workOrder->client->name }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 rounded-full text-sm font-bold 
                                @if($workOrder->status == 'concluida') bg-green-100 text-green-800 
                                @elseif($workOrder->status == 'cancelada') bg-red-100 text-red-800
                                @elseif($workOrder->status == 'em_execucao') bg-blue-100 text-blue-800
                                @elseif($workOrder->status == 'agendada') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
                            </span>
                            <p class="text-xs text-gray-400 mt-2">Criado em: {{ $workOrder->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-6 border-t pt-6">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase">Técnico Responsável</span>
                            <div class="mt-1 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-2">
                                    {{ substr($workOrder->technician->name ?? '?', 0, 2) }}
                                </div>
                                <span class="text-gray-900 font-medium">{{ $workOrder->technician->name ?? 'Não definido' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase">Agendamento</span>
                            <span class="block mt-1 text-gray-900 font-medium">
                                {{ $workOrder->scheduled_at ? $workOrder->scheduled_at->format('d/m/Y') : '-' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $workOrder->scheduled_at ? $workOrder->scheduled_at->format('H:i') : '' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase">Localização</span>
                            <span class="block mt-1 text-gray-900 font-medium">{{ $workOrder->service_location }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase">Tipo de Serviço</span>
                            <span class="block mt-1 text-gray-900 font-medium">{{ ucfirst($workOrder->service_type) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800">📋 Checklists & Documentação</h4>
                        </div>
                        <div class="p-6">
                            @if($workOrder->checklists->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Modelo</th>
                                                <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Status</th>
                                                <th class="text-right text-xs font-medium text-gray-500 uppercase tracking-wider pb-2">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($workOrder->checklists as $checklist)
                                                <tr>
                                                    <td class="py-3 text-sm font-medium text-gray-900">{{ $checklist->checklistModel->name }}</td>
                                                    <td class="py-3 text-sm">
                                                        @if($checklist->filled_at)
                                                            <span class="text-green-600 flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                Preenchido
                                                            </span>
                                                            <div class="text-xs text-gray-400">{{ $checklist->filled_at->format('d/m H:i') }}</div>
                                                        @else
                                                            <span class="text-yellow-600 flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                Pendente
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 text-sm text-right">
                                                        @if($checklist->filled_at)
                                                            <a href="{{ route('work-orders.checklistPdf', $checklist->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase hover:underline">
                                                                Ver PDF
                                                            </a>
                                                        @else
                                                            <span class="text-gray-300 text-xs italic">Aguardando</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">Nenhum checklist vinculado a esta missão.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800">📝 Escopo da Missão</h4>
                        </div>
                        <div class="p-6 text-gray-700 text-sm leading-relaxed whitespace-pre-wrap">
                            {{ $workOrder->description ?: 'Nenhuma descrição fornecida.' }}
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800">⏱️ Execução & Voo</h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <span class="text-xs text-gray-500 uppercase">Início Real (Check-in)</span>
                                <p class="font-mono text-gray-900 font-medium">
                                    {{ $workOrder->started_at ? $workOrder->started_at->format('d/m/Y H:i') : '--/--/---- --:--' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">Fim Real (Check-out)</span>
                                <p class="font-mono text-gray-900 font-medium">
                                    {{ $workOrder->finished_at ? $workOrder->finished_at->format('d/m/Y H:i') : '--/--/---- --:--' }}
                                </p>
                            </div>
                            <div class="border-t pt-4">
                                <span class="text-xs text-gray-500 uppercase">Protocolo DECEA</span>
                                <p class="text-gray-900 font-medium">{{ $workOrder->decea_protocol ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase">Altura Máxima</span>
                                <p class="text-gray-900 font-medium">{{ $workOrder->flight_max_altitude ? $workOrder->flight_max_altitude.' m' : 'Não informada' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800">🛰️ Equipamentos</h4>
                        </div>
                        <div class="p-6">
                            @if($workOrder->equipments->count() > 0)
                                <ul class="space-y-3">
                                    @foreach($workOrder->equipments as $eq)
                                        <li class="flex items-center">
                                            <span class="h-2 w-2 rounded-full bg-green-500 mr-2"></span>
                                            <span class="text-sm text-gray-700">{{ $eq->name }}</span>
                                            <span class="ml-auto text-xs text-gray-400 border border-gray-200 rounded px-1">{{ ucfirst($eq->type) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 italic">Nenhum equipamento registrado.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>