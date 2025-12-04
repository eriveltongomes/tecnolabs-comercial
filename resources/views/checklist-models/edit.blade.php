<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Modelo de Checklist') }}</h2>
    </x-slot>

    <div class="py-12" x-data="checklistBuilder()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('checklist-models.update', $checklistModel->id) }}">
                        @csrf @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border-b pb-4">
                            <div>
                                <x-input-label for="name" :value="__('Nome do Checklist')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$checklistModel->name" required />
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Tipo')" />
                                <select name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="aro" {{ $checklistModel->type == 'aro' ? 'selected' : '' }}>ARO (Análise de Risco)</option>
                                    <option value="pre_voo" {{ $checklistModel->type == 'pre_voo' ? 'selected' : '' }}>Pré-Voo</option>
                                    <option value="pos_voo" {{ $checklistModel->type == 'pos_voo' ? 'selected' : '' }}>Pós-Voo</option>
                                    <option value="instalacao" {{ $checklistModel->type == 'instalacao' ? 'selected' : '' }}>Instalação</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Descrição')" />
                                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="$checklistModel->description" />
                            </div>
                        </div>

                        <h3 class="text-lg font-bold mb-4 text-indigo-700">Itens de Verificação</h3>
                        
                        <div class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex flex-col space-y-3 p-4 bg-gray-50 rounded border border-gray-200">
                                    
                                    <div class="flex items-start space-x-3">
                                        <div class="pt-2 font-bold text-gray-400" x-text="index + 1 + '.'"></div>
                                        <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <input type="hidden" :name="'items['+index+'][id]'" x-model="item.id">
                                            <div class="md:col-span-2">
                                                <input type="text" :name="'items['+index+'][text]'" x-model="item.text" class="w-full border-gray-300 rounded-md" placeholder="Situação (Ex: Perda de Link)" required>
                                            </div>
                                        </div>
                                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 pt-2">Excluir</button>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 bg-white p-2 rounded border border-gray-100">
                                        <div><label class="text-xs text-gray-500 block">Probabilidade</label><input type="text" :name="'items['+index+'][probability]'" x-model="item.probability" class="w-full text-xs border-gray-200 rounded" placeholder="Ex: Remota"></div>
                                        <div><label class="text-xs text-gray-500 block">Severidade</label><input type="text" :name="'items['+index+'][severity]'" x-model="item.severity" class="w-full text-xs border-gray-200 rounded" placeholder="Ex: Leve"></div>
                                        <div><label class="text-xs text-gray-500 block">Risco</label><input type="text" :name="'items['+index+'][risk_level]'" x-model="item.risk_level" class="w-full text-xs border-gray-200 rounded" placeholder="Ex: 3E"></div>
                                        <div><label class="text-xs text-gray-500 block">Tolerabilidade</label><input type="text" :name="'items['+index+'][tolerability]'" x-model="item.tolerability" class="w-full text-xs border-gray-200 rounded" placeholder="Ex: Baixo"></div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div class="md:col-span-2">
                                            <input type="text" :name="'items['+index+'][mitigation]'" x-model="item.mitigation" class="w-full text-sm border-gray-300 rounded-md" placeholder="Medida de Mitigação (Ex: Usar checklist pré-voo)">
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <label class="inline-flex items-center"><input type="checkbox" :name="'items['+index+'][is_critical]'" x-model="item.is_critical" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500"><span class="ml-2 text-sm text-red-600 font-bold">Crítico?</span></label>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addItem()" class="mt-4 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 font-bold w-full border-2 border-dashed border-indigo-300">+ Adicionar Item</button>

                        <div class="mt-6 flex justify-between border-t pt-4">
                            <button type="button" onclick="if(confirm('Excluir?')) document.getElementById('delForm').submit()" class="text-red-600 hover:underline">Excluir Modelo</button>
                            <div><a href="{{ route('checklist-models.index') }}" class="mr-4 px-4 py-2 text-gray-600">Cancelar</a><x-primary-button>Salvar</x-primary-button></div>
                        </div>
                    </form>
                    <form id="delForm" action="{{ route('checklist-models.destroy', $checklistModel->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checklistBuilder() {
            return {
                items: @json($checklistModel->items).map(item => ({
                    id: item.id,
                    text: item.text,
                    help_text: item.help_text,
                    is_critical: !!item.is_critical,
                    probability: item.probability,
                    severity: item.severity,
                    risk_level: item.risk_level,
                    tolerability: item.tolerability, // Novo
                    mitigation: item.mitigation
                })),
                addItem() {
                    this.items.push({ id: null, text: '', help_text: '', is_critical: false, probability: '', severity: '', risk_level: '', tolerability: '', mitigation: '' });
                },
                removeItem(index) {
                    if (this.items.length > 1) this.items.splice(index, 1); else alert('Mínimo 1 item.');
                }
            }
        }
    </script>
</x-app-layout>