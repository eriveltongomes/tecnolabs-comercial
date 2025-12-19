<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nova OS Avulsa') }}</h2>
    </x-slot>

    <div class="py-12" x-data="workOrderForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('work-orders.store') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2 relative" x-data="{ open: false }">
                                <x-input-label for="client_search" :value="__('Cliente (Buscar por Nome ou CPF/CNPJ)')" />
                                
                                <x-text-input 
                                    id="client_search" 
                                    type="text" 
                                    class="block mt-1 w-full" 
                                    placeholder="Digite para buscar..."
                                    x-model="search"
                                    @focus="open = true"
                                    @click.outside="open = false"
                                    autocomplete="off"
                                />
                                
                                <input type="hidden" name="client_id" x-model="formData.client_id">

                                <div x-show="open && search.length > 0" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-60 overflow-y-auto" style="display: none;">
                                    <ul>
                                        <template x-for="client in filteredClients" :key="client.id">
                                            <li @click="selectClient(client)" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 flex justify-between items-center">
                                                <div>
                                                    <span class="font-bold block text-gray-800" x-text="client.name"></span>
                                                    <span class="text-xs text-gray-500" x-text="client.document ? client.document : 'Documento não cad.'"></span>
                                                </div>
                                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Selecionar</span>
                                            </li>
                                        </template>
                                        <li x-show="filteredClients.length === 0" class="px-4 py-2 text-gray-500 text-sm">Nenhum cliente encontrado com este nome.</li>
                                    </ul>
                                </div>
                                
                                <div x-show="selectedName" class="mt-2 text-sm text-green-600 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Selecionado: <span x-text="selectedName"></span>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="service_type" :value="__('Tipo de Serviço')" />
                                <select name="service_type" id="service_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="drone">Drone</option>
                                    <option value="timelapse">Timelapse</option>
                                    <option value="tour_virtual">Tour Virtual</option>
                                    <option value="manutencao">Manutenção</option>
                                    <option value="teste">Teste Interno</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="scheduled_at" :value="__('Data e Hora do Agendamento')" />
                                <x-text-input 
                                    id="scheduled_at" 
                                    class="block mt-1 w-full" 
                                    type="datetime-local" 
                                    name="scheduled_at" 
                                    :value="old('scheduled_at', now()->format('Y-m-d\TH:i'))" 
                                    required 
                                />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="title" :value="__('Título da OS')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required placeholder="Ex: Voo de Teste na Sede / Manutenção Câmera 01" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="service_location" :value="__('Local de Execução')" />
                                <x-text-input id="service_location" class="block mt-1 w-full" type="text" name="service_location" required placeholder="Endereço completo ou Coordenadas GPS" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Descrição / Escopo')" />
                                <textarea name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" placeholder="Descreva detalhadamente o que precisa ser feito..."></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
                            <a href="{{ route('work-orders.index') }}" class="mr-4 px-4 py-2 text-sm text-gray-600 hover:text-gray-900 underline">Cancelar</a>
                            <x-primary-button>Criar OS</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function workOrderForm() {
            return {
                // Aqui garantimos que os clientes carregam direto do Model, independente do Controller
                clients: @json(\App\Models\Client::select('id', 'name', 'document')->orderBy('name')->get()),
                search: '',
                selectedName: '',
                formData: {
                    client_id: ''
                },
                
                // Lógica de Filtro
                get filteredClients() {
                    if (this.search === '') return [];
                    return this.clients.filter(client => {
                        const term = this.search.toLowerCase();
                        const name = client.name.toLowerCase();
                        const doc = client.document ? client.document.toLowerCase() : '';
                        return name.includes(term) || doc.includes(term);
                    });
                },

                // Ao clicar na lista
                selectClient(client) {
                    this.formData.client_id = client.id;
                    this.selectedName = client.name;
                    this.search = client.name; 
                    this.open = false; 
                }
            }
        }
    </script>
</x-app-layout>