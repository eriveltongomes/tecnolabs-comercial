<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Gerenciar OS #') . $workOrder->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded border-l-4 border-indigo-500">
                        <div class="flex justify-between">
                            <div>
                                <h3 class="font-bold text-lg">{{ $workOrder->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $workOrder->client->name }} - {{ ucfirst($workOrder->service_type) }}</p>
                                <p class="mt-1 text-sm"><strong>Local:</strong> {{ $workOrder->service_location }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded text-xs font-bold uppercase 
                                    {{ $workOrder->status == 'pendente' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $workOrder->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        
                        <div class="lg:col-span-2">
                            <form method="POST" action="{{ route('work-orders.update', $workOrder->id) }}">
                                @csrf @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="font-bold mb-3 text-indigo-700">Agendamento</h4>
                                        <div class="mb-4">
                                            <x-input-label for="technician_id" :value="__('Piloto / Técnico')" />
                                            <select name="technician_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                                <option value="">Selecione...</option>
                                                @foreach($technicians as $tech)
                                                    <option value="{{ $tech->id }}" {{ $workOrder->technician_id == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <x-input-label for="scheduled_at" :value="__('Data e Hora')" />
                                            <x-text-input id="scheduled_at" class="block mt-1 w-full" type="datetime-local" name="scheduled_at" :value="$workOrder->scheduled_at ? $workOrder->scheduled_at->format('Y-m-d\TH:i') : ''" required />
                                        </div>
                                        <div class="mb-4">
                                            <x-input-label for="status" :value="__('Status Atual')" />
                                            <select name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                                <option value="pendente" {{ $workOrder->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                                <option value="agendada" {{ $workOrder->status == 'agendada' ? 'selected' : '' }}>Agendada</option>
                                                <option value="em_execucao" {{ $workOrder->status == 'em_execucao' ? 'selected' : '' }}>Em Execução</option>
                                                <option value="concluida" {{ $workOrder->status == 'concluida' ? 'selected' : '' }}>Concluída</option>
                                                <option value="cancelada" {{ $workOrder->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-bold mb-3 text-indigo-700">Regulatório & Equipamentos</h4>
                                        <div class="mb-4">
                                            <x-input-label for="decea_protocol" :value="__('Protocolo SARPAS/DECEA')" />
                                            <x-text-input id="decea_protocol" class="block mt-1 w-full" type="text" name="decea_protocol" :value="$workOrder->decea_protocol" placeholder="Ex: BR-2025-..." />
                                        </div>
                                        <div class="mb-4">
                                            <x-input-label for="flight_max_altitude" :value="__('Teto Máximo (m)')" />
                                            <x-text-input id="flight_max_altitude" class="block mt-1 w-full" type="number" name="flight_max_altitude" :value="$workOrder->flight_max_altitude" placeholder="Ex: 120" />
                                        </div>
                                        <div class="mb-4">
                                            <x-input-label for="service_location" :value="__('Local')" />
                                            <x-text-input id="service_location" class="block mt-1 w-full" type="text" name="service_location" :value="$workOrder->service_location" required />
                                        </div>
                                        
                                        <div class="mt-6">
                                            <x-input-label for="equipments" :value="__('Equipamentos Alocados (Multi-select)')" />
                                            <p class="text-xs text-gray-500 mb-1">Segure Ctrl (Win) ou Cmd (Mac) para selecionar vários.</p>
                                            <select name="equipments[]" id="equipments" multiple class="block w-full border-gray-300 rounded-md shadow-sm h-32">
                                                @foreach($allEquipments as $eq)
                                                    <option value="{{ $eq->id }}" 
                                                        {{ $workOrder->equipments->contains($eq->id) ? 'selected' : '' }}>
                                                        {{ $eq->name }} {{ $eq->anac_registration ? '('.$eq->anac_registration.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <x-input-label for="description" :value="__('Detalhamento Técnico (Escopo)')" />
                                    <textarea name="description" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" rows="4">{{ $workOrder->description }}</textarea>
                                </div>

                                <div class="mt-6 flex justify-end border-t pt-4">
                                    <a href="{{ route('work-orders.index') }}" class="mr-4 px-4 py-2 text-gray-600">Voltar</a>
                                    <x-primary-button>Salvar Agendamento</x-primary-button>
                                </div>
                            </form>
                        </div>

                        <div class="lg:col-span-1 border-l border-gray-200 pl-8">
                            <h4 class="font-bold mb-4 text-indigo-700 text-lg">Checklists Obrigatórios</h4>
                            <div class="bg-yellow-50 p-3 rounded text-xs text-yellow-800 mb-4">
                                Adicione aqui os formulários que o piloto deverá preencher no app.
                            </div>

                            <ul class="mb-6 space-y-3">
                                @forelse($workOrder->checklists as $checklist)
                                    <li class="flex justify-between items-center bg-gray-100 p-3 rounded border">
                                        <div>
                                            <span class="font-bold text-sm block text-gray-800">{{ $checklist->checklistModel->name }}</span>
                                            <span class="text-xs font-bold uppercase {{ $checklist->filled_at ? 'text-green-600' : 'text-red-500' }}">
                                                {{ $checklist->filled_at ? 'Preenchido' : 'Pendente' }}
                                            </span>
                                        </div>
                                        @if(!$checklist->filled_at)
                                            <form action="{{ route('work-orders.removeChecklist', $checklist->id) }}" method="POST" onsubmit="return confirm('Remover este checklist?');">
                                                @csrf @method('DELETE')
                                                <button class="text-red-500 hover:text-red-700 font-bold px-2">X</button>
                                            </form>
                                        @else
                                            <span class="text-green-500">✓</span>
                                        @endif
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-400 italic text-center border-2 border-dashed border-gray-200 p-4 rounded">
                                        Nenhum checklist vinculado.<br>Adicione abaixo.
                                    </li>
                                @endforelse
                            </ul>

                            <form action="{{ route('work-orders.addChecklist', $workOrder->id) }}" method="POST" class="mt-6 pt-6 border-t border-gray-200">
                                @csrf
                                <x-input-label for="checklist_model_id" :value="__('Adicionar Modelo')" />
                                <div class="flex gap-2 mt-1">
                                    <select name="checklist_model_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        @foreach($availableModels as $model)
                                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 text-lg font-bold">+</button>
                                </div>
                                @if($availableModels->isEmpty())
                                    <p class="text-xs text-red-500 mt-2">Nenhum modelo cadastrado. <a href="{{ route('checklist-models.create') }}" class="underline">Crie um aqui</a>.</p>
                                @endif
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>