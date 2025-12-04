<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Execução de Serviço</h2>
            <a href="{{ route('work-orders.myServices') }}" class="text-sm text-gray-600 underline">Voltar</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 
                {{ $workOrder->status == 'em_execucao' ? 'border-green-500' : 'border-blue-500' }}">
                <div class="p-6">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-500 text-xs uppercase">OS #{{ $workOrder->id }}</span>
                        <span class="px-2 py-1 text-xs font-bold rounded uppercase {{ $workOrder->status == 'em_execucao' ? 'bg-green-100 text-green-800' : 'bg-gray-100' }}">
                            {{ str_replace('_', ' ', $workOrder->status) }}
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold mt-2">{{ $workOrder->title }}</h3>
                    <p class="text-gray-600">{{ $workOrder->client->name }}</p>
                    
                    <div class="mt-4 text-sm bg-gray-50 p-3 rounded">
                        <p><strong>Local:</strong> {{ $workOrder->service_location }}</p>
                        @if($workOrder->decea_protocol)
                        <p><strong>SARPAS:</strong> {{ $workOrder->decea_protocol }} (Max: {{ $workOrder->flight_max_altitude }}m)</p>
                        @endif
                        @if($workOrder->description)
                        <p class="mt-2 text-gray-500 italic">"{{ $workOrder->description }}"</p>
                        @endif
                    </div>

                    <div class="mt-6">
                        @if($workOrder->status == 'agendada')
                            <form action="{{ route('work-orders.updateStatus', $workOrder->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="em_execucao">
                                <button class="w-full py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 text-lg">🚀 INICIAR SERVIÇO</button>
                            </form>
                        @elseif($workOrder->status == 'em_execucao')
                            <form action="{{ route('work-orders.updateStatus', $workOrder->id) }}" method="POST" onsubmit="return confirm('Finalizar?');">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="concluida">
                                <button class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg shadow-lg hover:bg-indigo-700 text-lg">🏁 FINALIZAR SERVIÇO</button>
                            </form>
                        @elseif($workOrder->status == 'concluida')
                            <div class="w-full py-3 bg-gray-200 text-gray-500 font-bold rounded-lg text-center">Concluído em {{ $workOrder->finished_at->format('d/m H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <h3 class="font-bold text-lg text-gray-700 px-2">Formulários Obrigatórios</h3>

            @foreach($workOrder->checklists as $checklist)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ open: {{ $checklist->filled_at ? 'false' : 'true' }} }">
                    <div @click="open = !open" class="p-4 flex justify-between items-center cursor-pointer bg-gray-50 border-b">
                        <div class="flex items-center">
                            @if($checklist->filled_at) <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else <div class="w-6 h-6 border-2 border-gray-300 rounded-full mr-2"></div> @endif
                            <span class="font-bold {{ $checklist->filled_at ? 'text-green-700' : 'text-gray-700' }}">{{ $checklist->checklistModel->name }}</span>
                        </div>
                        <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <div x-show="open" class="p-6 border-t border-gray-100">
                        @if($checklist->filled_at)
                            <div class="space-y-4">
                                <div class="flex justify-between items-start">
                                    <div class="text-sm text-gray-500 mb-2">Preenchido por {{ $checklist->user->name ?? '...' }} em {{ $checklist->filled_at->format('d/m H:i') }}</div>
                                    <a href="{{ route('work-orders.checklistPdf', $checklist->id) }}" target="_blank" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded hover:bg-red-200 border border-red-200">Baixar PDF</a>
                                </div>
                                @foreach($checklist->answers as $answer)
                                    <div class="flex justify-between border-b pb-2">
                                        <span class="text-gray-700 w-2/3">{{ $answer->checklistItem->text }}</span>
                                        <span class="font-bold {{ $answer->is_ok ? 'text-green-600' : 'text-red-600' }}">{{ $answer->is_ok ? 'CONFORME' : 'NÃO CONFORME' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <form action="{{ route('work-orders.saveChecklist', $checklist->id) }}" method="POST">
                                @csrf
                                <div class="space-y-6">
                                    @foreach($checklist->checklistModel->items as $item)
                                        <div class="border rounded-lg overflow-hidden" x-data="{ status: 'ok' }">
                                            <div class="bg-gray-100 p-3 border-b">
                                                <p class="font-bold text-gray-900">{{ $item->text }}</p>
                                                
                                                @if($item->mitigation || $item->risk_level)
                                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-600">
                                                    <div><span class="font-bold">Prob:</span> {{ $item->probability ?? '-' }}</div>
                                                    <div><span class="font-bold">Sev:</span> {{ $item->severity ?? '-' }}</div>
                                                    <div><span class="font-bold">Risco:</span> {{ $item->risk_level ?? '-' }}</div>
                                                    <div><span class="font-bold">Toler:</span> {{ $item->tolerability ?? '-' }}</div>
                                                </div>
                                                @endif
                                            </div>

                                            <div class="p-4 bg-white">
                                                @if($item->mitigation)
                                                    <div class="mb-4 p-3 bg-yellow-50 text-yellow-800 text-sm rounded border border-yellow-200">
                                                        <strong>🛡️ Medida de Mitigação Obrigatória:</strong><br>
                                                        {{ $item->mitigation }}
                                                    </div>
                                                    <p class="text-sm font-bold text-indigo-700 mb-2">A medida foi aplicada / Situação controlada?</p>
                                                @else
                                                    <p class="text-sm font-bold text-indigo-700 mb-2">Conforme?</p>
                                                @endif

                                                <div class="flex space-x-4">
                                                    <label class="flex items-center space-x-2 cursor-pointer bg-green-50 px-3 py-2 rounded border border-green-200 w-1/2 justify-center">
                                                        <input type="radio" name="answers[{{ $item->id }}][ok]" value="1" x-model="status" class="text-green-600 focus:ring-green-500">
                                                        <span class="text-sm font-bold text-green-700">SIM / OK</span>
                                                    </label>
                                                    <label class="flex items-center space-x-2 cursor-pointer bg-red-50 px-3 py-2 rounded border border-red-200 w-1/2 justify-center">
                                                        <input type="radio" name="answers[{{ $item->id }}][ok]" value="0" x-model="status" class="text-red-600 focus:ring-red-500">
                                                        <span class="text-sm font-bold text-red-700">NÃO</span>
                                                    </label>
                                                </div>

                                                <div x-show="status == '0'" class="mt-3">
                                                    @if($item->is_critical) <p class="text-red-600 text-xs font-bold mb-1">⚠️ ITEM CRÍTICO: Operação pode ser abortada.</p> @endif
                                                    <input type="text" name="answers[{{ $item->id }}][obs]" placeholder="Justifique o problema..." class="w-full text-sm border-red-300 rounded-md focus:ring-red-500">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($checklist->checklistModel->type == 'aro')
                                    <div class="mt-6 bg-yellow-50 p-4 rounded border border-yellow-200">
                                        <label class="block font-bold text-yellow-800 mb-2">Avaliação Global de Risco (Pelo Piloto)</label>
                                        <select name="risk_level" class="w-full border-yellow-300 rounded-md" required>
                                            <option value="baixo">Baixo Risco (Verde)</option>
                                            <option value="medio">Médio Risco (Amarelo)</option>
                                            <option value="alto">Alto Risco (Vermelho)</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <label class="text-sm text-gray-600">Observações Gerais</label>
                                    <textarea name="comments" class="w-full border-gray-300 rounded-md h-20"></textarea>
                                </div>

                                <button type="submit" class="mt-6 w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow">Salvar e Assinar Checklist</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
            
            @if($workOrder->checklists->count() == 0) <p class="text-center text-gray-500 italic">Nenhum checklist vinculado.</p> @endif
        </div>
    </div>
</x-app-layout>