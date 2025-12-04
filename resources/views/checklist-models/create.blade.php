<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Novo Modelo de Checklist') }}</h2>
    </x-slot>

    <div class="py-12" x-data="checklistBuilder()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('checklist-models.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border-b pb-4">
                            <div>
                                <x-input-label for="name" :value="__('Nome do Checklist')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required placeholder="Ex: ARO Drone Padrão" />
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Tipo')" />
                                <select name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="aro">ARO (Análise de Risco)</option>
                                    <option value="pre_voo">Pré-Voo (Equipamento)</option>
                                    <option value="pos_voo">Pós-Voo</option>
                                    <option value="instalacao">Instalação</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Descrição (Opcional)')" />
                                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" />
                            </div>
                        </div>

                        <h3 class="text-lg font-bold mb-4 text-indigo-700">Perguntas / Itens de Verificação</h3>
                        
                        <div class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded border border-gray-200">
                                    <div class="pt-2 font-bold text-gray-400" x-text="index + 1 + '.'"></div>
                                    
                                    <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="md:col-span-2">
                                            <input type="text" :name="'items['+index+'][text]'" x-model="item.text" class="w-full border-gray-300 rounded-md" placeholder="Ex: Condições meteorológicas favoráveis?" required>
                                        </div>
                                        <div>
                                            <input type="text" :name="'items['+index+'][help_text]'" x-model="item.help_text" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ajuda (Ex: Vento < 30km/h)">
                                        </div>
                                        <div class="flex items-center">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" :name="'items['+index+'][is_critical]'" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                                <span class="ml-2 text-sm text-red-600 font-bold">Item Crítico (Bloqueante)</span>
                                            </label>
                                        </div>
                                    </div>

                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 pt-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addItem()" class="mt-4 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 font-bold w-full border-2 border-dashed border-indigo-300">
                            + Adicionar Pergunta
                        </button>

                        <div class="mt-6 flex justify-end border-t pt-4">
                            <a href="{{ route('checklist-models.index') }}" class="mr-4 px-4 py-2 text-gray-600">Cancelar</a>
                            <x-primary-button>Salvar Modelo</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checklistBuilder() {
            return {
                items: [
                    { text: '', help_text: '', is_critical: false } // Começa com 1 item
                ],
                addItem() {
                    this.items.push({ text: '', help_text: '', is_critical: false });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        alert('O checklist deve ter pelo menos um item.');
                    }
                }
            }
        }
    </script>
</x-app-layout>