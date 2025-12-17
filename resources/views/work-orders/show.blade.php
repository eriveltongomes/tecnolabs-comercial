<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da OS #') . $workOrder->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('work-orders.pdf', $workOrder->id) }}" target="_blank" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold text-xs uppercase hover:bg-indigo-700 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Baixar OS
                </a>

                @if(in_array(Auth::user()->role, ['admin', 'financeiro', 'comercial'])) 
                    <a href="{{ route('work-orders.edit', $workOrder->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md font-bold text-xs uppercase hover:bg-yellow-600">
                        Editar / Agendar
                    </a>
                @endif
                <a href="{{ route('work-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-bold text-xs uppercase hover:bg-gray-300">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 p-4 rounded-lg flex justify-between items-center border-l-4
                {{ $workOrder->status == 'concluida' ? 'bg-green-100 border-green-500 text-green-800' : 
                  ($workOrder->status == 'pendente' ? 'bg-red-100 border-red-500 text-red-800' : 'bg-blue-100 border-blue-500 text-blue-800') }}">
                <div>
                    <span class="font-bold uppercase text-sm tracking-wider">Status: {{ str_replace('_', ' ', $workOrder->status) }}</span>
                </div>
                <div class="text-sm">
                    @if($workOrder->started_at) Início: <strong>{{ $workOrder->started_at->format('d/m H:i') }}</strong> @endif
                    @if($workOrder->finished_at) | Fim: <strong>{{ $workOrder->finished_at->format('d/m H:i') }}</strong> @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informações do Serviço</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="text-xs text-gray-500 font-bold uppercase">Título</label><p class="text-lg">{{ $workOrder->title }}</p></div>
                                <div><label class="text-xs text-gray-500 font-bold uppercase">Cliente</label><p class="text-lg">{{ $workOrder->client->name }}</p></div>
                                <div><label class="text-xs text-gray-500 font-bold uppercase">Tipo</label><p class="text-gray-800">{{ ucfirst($workOrder->service_type) }}</p></div>
                                <div><label class="text-xs text-gray-500 font-bold uppercase">Local</label><p class="text-gray-800">{{ $workOrder->service_location }}</p></div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded mb-4">
                                <label class="text-xs text-gray-500 font-bold uppercase">Descrição / Escopo</label>
                                <p class="text-gray-700 mt-1 whitespace-pre-line">{{ $workOrder->description ?? 'Sem descrição.' }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-xs text-gray-500 font-bold uppercase">Técnico Responsável</label>
                                    <div class="flex items-center mt-1">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>{{ $workOrder->technician->name ?? 'Não atribuído' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 font-bold uppercase">Agendamento</label>
                                    <p>{{ $workOrder->scheduled_at ? $workOrder->scheduled_at->format('d/m/Y H:i') : 'Pendente' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 font-bold uppercase">Protocolo DECEA</label>
                                    <p class="font-mono">{{ $workOrder->decea_protocol ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Equipamentos Alocados</h3>
                            @if($workOrder->equipments->count() > 0)
                                <ul class="space-y-2">
                                    @foreach($workOrder->equipments as $eq)
                                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                                        <span class="font-bold">{{ $eq->name }}</span>
                                        <span class="text-xs text-gray-500">ANAC: {{ $eq->anac_registration ?? 'N/A' }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500 italic text-sm">Nenhum equipamento vinculado.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Checklists / ARO</h3>
                            <ul class="space-y-3">
                                @forelse($workOrder->checklists as $checklist)
                                    <li class="border rounded p-3 {{ $checklist->filled_at ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="font-bold text-sm">{{ $checklist->checklistModel->name }}</span>
                                            @if($checklist->filled_at)
                                                <span class="text-xs bg-green-200 text-green-800 px-2 py-0.5 rounded font-bold">OK</span>
                                            @else
                                                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded">Pendente</span>
                                            @endif
                                        </div>
                                        
                                        @if($checklist->filled_at)
                                            <div class="text-xs text-gray-600 mb-2">
                                                Por: {{ $checklist->user->name }}<br>
                                                Em: {{ $checklist->filled_at->format('d/m H:i') }}
                                            </div>
                                            @if($checklist->checklistModel->type == 'aro')
                                                <div class="text-xs font-bold mb-2">Risco: <span class="uppercase">{{ $checklist->risk_level }}</span></div>
                                            @endif
                                            
                                            <a href="{{ route('work-orders.checklistPdf', $checklist->id) }}" target="_blank" class="block w-full text-center py-1 bg-white border border-gray-300 rounded text-xs hover:bg-gray-50 text-indigo-600 font-bold">
                                                Baixar PDF
                                            </a>
                                        @else
                                            <p class="text-xs text-gray-400 italic">Aguardando preenchimento pelo piloto.</p>
                                        @endif
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500 italic">Nenhum checklist vinculado.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    @if(Auth::user()->role === 'admin')
                    <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg border border-red-100">
                        <div class="p-6 text-red-900">
                            <h3 class="font-bold text-sm mb-2 text-red-700">Zona de Perigo</h3>
                            <form action="{{ route('work-orders.destroy', $workOrder->id) }}" method="POST" onsubmit="return confirm('Tem certeza absoluta? Isso apagará todo o histórico desta OS, incluindo checklists preenchidos.');">
                                @csrf @method('DELETE')
                                <button class="w-full py-2 bg-red-600 text-white rounded font-bold text-sm hover:bg-red-700">Excluir Ordem de Serviço</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>