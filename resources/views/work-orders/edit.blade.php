<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciar Ordem de Serviço') }} <span class="text-indigo-600">#{{ $workOrder->id }}</span>
            </h2>
            <span class="px-3 py-1 rounded-full text-sm font-bold 
                @if($workOrder->status == 'concluida') bg-green-100 text-green-800 
                @elseif($workOrder->status == 'cancelada') bg-red-100 text-red-800
                @elseif($workOrder->status == 'em_execucao') bg-blue-100 text-blue-800
                @elseif($workOrder->status == 'agendada') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">📋 Checklists de Segurança</h3>
                        <span class="text-xs text-gray-500">Documentos vinculados a esta missão</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            @if($workOrder->checklists->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preenchido em</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($workOrder->checklists as $checklist)
                                                <tr>
                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                        {{ $checklist->checklistModel->name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">
                                                        @if($checklist->filled_at)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                Concluído
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                Pendente
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-500">
                                                        {{ $checklist->filled_at ? $checklist->filled_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-right">
                                                        @if(!$checklist->filled_at)
                                                            <form action="{{ route('work-orders.removeChecklist', $checklist->id) }}" method="POST" onsubmit="return confirm('Remover este checklist?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-xs uppercase hover:underline">
                                                                    Remover
                                                                </button>
                                                            </form>
                                                        @else
                                                            <a href="{{ route('work-orders.checklistPdf', $checklist->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-medium text-xs uppercase hover:underline">
                                                                Ver PDF
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                    <div class="flex">
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                Nenhum checklist vinculado. Adicione modelos (Ex: ARO, Pré-Voo) para o piloto preencher.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 h-fit">
                            <h4 class="font-medium text-gray-900 mb-3 text-sm">Adicionar Novo Checklist</h4>
                            <form action="{{ route('work-orders.addChecklist', $workOrder->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <select name="checklist_model_id" class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">Selecione um Modelo...</option>
                                        @foreach($availableModels as $model)
                                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    + Vincular
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="mb-6 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">📝 Detalhes da Missão</h3>
                        <p class="text-sm text-gray-500">Edite os dados principais, agendamento e equipamentos.</p>
                    </div>

                    <form action="{{ route('work-orders.update', $workOrder->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Cliente</label>
                                <input type="text" value="{{ $workOrder->client->name }}" disabled class="bg-gray-100 border-gray-300 rounded-md shadow-sm w-full mt-1 text-gray-600">
                            </div>

                            <div>
                                <label for="technician_id" class="block font-medium text-sm text-gray-700">Técnico Responsável (Piloto)</label>
                                <select name="technician_id" id="technician_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1 bg-white" required>
                                    <option value="">Selecione...</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" {{ $workOrder->technician_id == $tech->id ? 'selected' : '' }}>
                                            {{ $tech->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700">Título da OS</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $workOrder->title) }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                            </div>

                            <div>
                                <label for="service_location" class="block font-medium text-sm text-gray-700">Local do Serviço</label>
                                <input type="text" name="service_location" id="service_location" value="{{ old('service_location', $workOrder->service_location) }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block font-medium text-sm text-gray-700">Escopo / Descrição</label>
                                <textarea name="description" id="description" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('description', $workOrder->description) }}</textarea>
                            </div>

                            <div>
                                <label for="scheduled_at" class="block font-medium text-sm text-gray-700">Data Agendada (Prevista)</label>
                                <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                                       value="{{ old('scheduled_at', $workOrder->scheduled_at ? $workOrder->scheduled_at->format('Y-m-d\TH:i') : '') }}" 
                                       class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                            </div>

                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700">Status Atual</label>
                                <select name="status" id="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                    <option value="pendente" {{ $workOrder->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="agendada" {{ $workOrder->status == 'agendada' ? 'selected' : '' }}>Agendada</option>
                                    <option value="em_execucao" {{ $workOrder->status == 'em_execucao' ? 'selected' : '' }}>Em Execução</option>
                                    <option value="concluida" {{ $workOrder->status == 'concluida' ? 'selected' : '' }}>Concluída</option>
                                    <option value="cancelada" {{ $workOrder->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>

                            <div>
                                <label for="decea_protocol" class="block font-medium text-sm text-gray-700">Protocolo DECEA (SARPAS)</label>
                                <input type="text" name="decea_protocol" id="decea_protocol" value="{{ old('decea_protocol', $workOrder->decea_protocol) }}" placeholder="Ex: BR-..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                            </div>

                            <div>
                                <label for="flight_max_altitude" class="block font-medium text-sm text-gray-700">Altura Máxima (m)</label>
                                <input type="number" name="flight_max_altitude" id="flight_max_altitude" value="{{ old('flight_max_altitude', $workOrder->flight_max_altitude) }}" placeholder="Ex: 120" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-3 text-md">🛰️ Equipamentos Utilizados</h4>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @if(isset($allEquipments) && $allEquipments->count() > 0)
                                        @foreach($allEquipments as $eq)
                                            <label class="inline-flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded transition">
                                                <input type="checkbox" name="equipments[]" value="{{ $eq->id }}" 
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-5 w-5"
                                                    @if($workOrder->equipments->contains($eq->id)) checked @endif>
                                                <span class="ml-2 text-sm text-gray-700 font-medium">{{ $eq->name }}</span>
                                                <span class="ml-1 text-xs text-gray-400">({{ ucfirst($eq->type) }})</span>
                                            </label>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-gray-500 italic">Nenhum equipamento cadastrado.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-2 text-md">⏱️ Correção de Histórico (Datas Reais)</h4>
                            <p class="text-xs text-gray-500 mb-4">Use estes campos apenas para corrigir relatórios de OSs que não tiveram o "Start/Stop" registrado pelo app.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                                <div>
                                    <label class="block font-medium text-sm text-yellow-800">Início Real (Check-in)</label>
                                    <input type="datetime-local" name="started_at" 
                                        value="{{ $workOrder->started_at ? $workOrder->started_at->format('Y-m-d\TH:i') : '' }}" 
                                        class="border-yellow-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm w-full mt-1">
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-yellow-800">Fim Real (Check-out)</label>
                                    <input type="datetime-local" name="finished_at" 
                                        value="{{ $workOrder->finished_at ? $workOrder->finished_at->format('Y-m-d\TH:i') : '' }}" 
                                        class="border-yellow-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm w-full mt-1">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 pt-4 border-t border-gray-200">
                            <a href="{{ route('work-orders.index') }}" class="text-gray-500 hover:text-gray-900 mr-6 font-medium text-sm uppercase tracking-wide">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-lg transform hover:scale-105 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>