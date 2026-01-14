<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editando Proposta #') . $proposal->proposal_number }}
            </h2>
            <a href="{{ route('proposals.index') }}" class="px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition ease-in-out duration-150">Voltar</a>
        </div>
    </x-slot>

    <div class="py-12" x-data="proposalData()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded-lg flex justify-between items-center">
                <span>Status Atual: <strong>{{ ucfirst($proposal->status) }}</strong></span>
                @if($proposal->status == 'reprovada') <span class="text-sm text-red-600 font-bold">Motivo: {{ $proposal->rejection_reason }}</span> @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('proposals.update', $proposal->id) }}" id="proposalForm">
                        @csrf @method('PUT')
                        <input type="hidden" name="total_value" x-model="results.final_price">

                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">1. Dados Gerais</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <x-input-label for="client_id" :value="__('Cliente')" />
                                    <select name="client_id" x-model="formData.client_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100" required>
                                        @foreach($clients as $client) <option value="{{ $client->id }}">{{ $client->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="channel_id" :value="__('Canal')" />
                                    <select name="channel_id" x-model="formData.channel_id" class="block mt-1 w-full border-gray-300 rounded-md" required>
                                        @foreach($channels as $channel) <option value="{{ $channel->id }}">{{ $channel->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="service_type" :value="__('Serviço')" />
                                    <select name="service_type" x-model="serviceType" class="block mt-1 w-full border-gray-300 rounded-md" required>
                                        <option value="drone">Drone</option>
                                        <option value="tour_virtual">Tour Virtual</option>
                                        <option value="timelapse">Timelapse</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select name="status" class="block mt-1 w-full border-gray-300 rounded-md">
                                        <option value="rascunho" {{ $proposal->status == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                                        <option value="aberta" {{ $proposal->status == 'aberta' ? 'selected' : '' }}>Aberta</option>
                                        <option value="cancelada" {{ $proposal->status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-b pb-4 mb-4 bg-blue-50 p-4 rounded">
                            <h3 class="text-lg font-medium text-blue-900 mb-4">Informações do Documento</h3>
                            <div class="mb-4">
                                <x-input-label for="scope_description" :value="__('Escopo do Serviço')" />
                                <textarea id="scope_description" name="scope_description" x-model="formData.scope_description" class="block mt-1 w-full border-gray-300 rounded-md" rows="4" required></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><x-input-label for="service_location" :value="__('Local')" /><x-text-input id="service_location" class="block mt-1 w-full" type="text" name="service_location" x-model="formData.service_location" required /></div>
                                <div><x-input-label for="service_date" :value="__('Data')" /><x-text-input id="service_date" class="block mt-1 w-full" type="date" name="service_date" x-model="formData.service_date" required /></div>
                                <div class="md:col-span-2"><x-input-label for="payment_terms" :value="__('Pagamento')" /><x-text-input id="payment_terms" class="block mt-1 w-full" type="text" name="payment_terms" x-model="formData.payment_terms" required /></div>
                                <div class="md:col-span-2"><x-input-label for="courtesy" :value="__('Cortesia')" /><x-text-input id="courtesy" class="block mt-1 w-full" type="text" name="courtesy" x-model="formData.courtesy" /></div>
                            </div>
                        </div>

                        <div class="border-b pb-4 mb-4 bg-indigo-50 p-4 rounded">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div class="col-span-2"><h3 class="text-lg font-medium text-indigo-900">Margem de Lucro</h3></div>
                                <div><x-input-label for="profit_margin" :value="__('%')" /><x-text-input x-model="formData.profit_margin" class="block mt-1 w-full" type="number" step="0.01" name="profit_margin" required /></div>
                            </div>
                        </div>

                        <div class="border-b pb-4 mb-4 bg-gray-50 p-4 rounded">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes Operacionais</h3>
                            <div x-show="serviceType === 'drone' || serviceType === 'tour_virtual'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label value="Equipamento" />
                                    <select name="details[equipment_id]" x-model="formData.details.equipment_id" class="block mt-1 w-full border-gray-300 rounded-md">
                                        <option value="">Nenhum</option>
                                        @foreach($equipments as $eq) <option value="{{ $eq->id }}">{{ $eq->name }}</option> @endforeach
                                    </select>
                                </div>
                                <div><x-input-label value="Mão de Obra (R$)" /><x-text-input x-model="formData.details.labor_cost" @input="maskMoney($event, 'details.labor_cost')" class="block mt-1 w-full" name="details[labor_cost]" /></div>
                                <div>
                                    <x-input-label value="Período" />
                                    <select x-model="formData.details.period_hours" name="details[period_hours]" class="block mt-1 w-full border-gray-300 rounded-md">
                                        <option value="1">1 Hora</option>
                                        <option value="4">Meia Diária (4h)</option>
                                        <option value="8">Diária Completa (8h)</option>
                                    </select>
                                </div>
                            </div>
                            <div x-show="serviceType === 'timelapse'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><x-input-label value="Mensalidade (R$)" /><x-text-input x-model="formData.details.monthly_cost" @input="maskMoney($event, 'details.monthly_cost')" class="block mt-1 w-full" name="details[monthly_cost]" /></div>
                                <div><x-input-label value="Meses" /><x-text-input x-model="formData.details.months" class="block mt-1 w-full" type="number" name="details[months]" /></div>
                                <div><x-input-label value="Instalação (R$)" /><x-text-input x-model="formData.details.installation_cost" @input="maskMoney($event, 'details.installation_cost')" class="block mt-1 w-full" name="details[installation_cost]" /></div>
                            </div>
                        </div>

                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Logística</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div><x-input-label value="Combustível" /><x-text-input x-model="formData.variable_costs.fuel" @input="maskMoney($event, 'variable_costs.fuel')" name="variable_costs[fuel]" class="block mt-1 w-full" /></div>
                                <div><x-input-label value="Hospedagem" /><x-text-input x-model="formData.variable_costs.hotel" @input="maskMoney($event, 'variable_costs.hotel')" name="variable_costs[hotel]" class="block mt-1 w-full" /></div>
                                <div><x-input-label value="Alimentação" /><x-text-input x-model="formData.variable_costs.food" @input="maskMoney($event, 'variable_costs.food')" name="variable_costs[food]" class="block mt-1 w-full" /></div>
                                <div><x-input-label value="Outros" /><x-text-input x-model="formData.variable_costs.other" @input="maskMoney($event, 'variable_costs.other')" name="variable_costs[other]" class="block mt-1 w-full" /></div>
                            </div>
                        </div>

                        <div class="bg-gray-800 text-white p-6 rounded-lg">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                                <div><span class="block text-gray-400">Custo Base</span><span class="font-bold text-lg" x-text="formatMoney(results.total_cost)"></span></div>
                                <div><span class="block text-gray-400">Impostos</span><span class="font-bold text-lg" x-text="results.taxes_percent + '%'"></span></div>
                                <div><span class="block text-gray-400">Comissão</span><span class="font-bold text-lg text-yellow-400" x-text="results.commission_percent + '%'"></span></div>
                                <div><span class="block text-gray-400">Valor Comissão</span><span class="font-bold text-lg" x-text="formatMoney(results.commission_value)"></span></div>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-600 pt-4">
                                <div class="flex-grow flex items-center">
                                    <div class="text-2xl font-bold mr-6">Total: <span class="text-green-400" x-text="formatMoney(results.final_price)"></span></div>
                                    
                                    <div x-show="serviceType === 'timelapse'" class="border-l border-gray-600 pl-6">
                                        <span class="block text-gray-400 text-xs uppercase">Mensalidade (Cliente)</span>
                                        <span class="font-bold text-xl text-yellow-400" x-text="formatMoney(results.estimated_monthly)"></span>
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <a href="{{ route('proposals.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded text-white font-bold flex items-center">Cancelar</a>
                                    <button type="button" @click="calculate()" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 rounded text-white font-bold">Recalcular</button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white font-bold">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    @if($proposal->status == 'rascunho' || $proposal->status == 'aberta')
                    <div class="mt-6 text-right border-t pt-4">
                        <p class="text-gray-600 text-sm mb-2">Enviar:</p>
                        <form action="{{ route('proposals.sendToAnalysis', $proposal->id) }}" method="POST" onsubmit="return confirm('Enviar?');">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded text-white font-bold shadow-lg uppercase tracking-wider">Enviar para Aprovação</button>
                        </form>
                    </div>
                    @endif
                    <form id="cancelForm" action="{{ route('proposals.cancel', $proposal->id) }}" method="POST" style="display:none">@csrf @method('PATCH')</form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function proposalData() {
            const details = @json($proposal->service_details ?? []);
            const vars = @json($variableCosts ?? []);
            
            return {
                serviceType: '{{ $proposal->service_type }}',
                formData: {
                    client_id: '{{ $proposal->client_id }}',
                    channel_id: '{{ $proposal->channel_id }}',
                    // CORREÇÃO: Carrega a margem do banco
                    profit_margin: {{ $proposal->profit_margin ?? 20 }},
                    
                    service_location: '{{ $proposal->service_location }}',
                    service_date: '{{ $proposal->service_date ? $proposal->service_date->format("Y-m-d") : "" }}',
                    payment_terms: '{{ $proposal->payment_terms }}',
                    courtesy: '{{ $proposal->courtesy }}',
                    scope_description: {!! json_encode($proposal->scope_description) !!},
                    
                    details: {
                        period_hours: details.period_hours || 4,
                        months: details.months || 1,
                        labor_cost: details.labor_cost ? formatBRL(details.labor_cost) : '',
                        monthly_cost: details.monthly_cost ? formatBRL(details.monthly_cost) : '',
                        installation_cost: details.installation_cost ? formatBRL(details.installation_cost) : '',
                        equipment_id: details.equipment_id || ''
                    },
                    variable_costs: {
                        fuel: vars['fuel'] ? formatBRL(vars['fuel']) : '',
                        hotel: vars['hotel'] ? formatBRL(vars['hotel']) : '',
                        food: vars['food'] ? formatBRL(vars['food']) : '',
                        other: vars['other'] ? formatBRL(vars['other']) : ''
                    }
                },
                results: { total_cost: 0, taxes_percent: 0, commission_percent: 0, commission_value: 0, final_price: {{ $proposal->total_value ?? 0 }}, estimated_monthly: 0 },
                maskMoney(e, modelPath) { 
                    let value = e.target.value.replace(/\D/g, "");
                    value = (value / 100).toFixed(2) + "";
                    value = value.replace(".", ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                    e.target.value = value;
                    let path = modelPath.split('.');
                    if(path.length === 2) this.formData[path[0]][path[1]] = value;
                },
                formatMoney(value) { return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0); },
                async calculate() {
                    const payload = {
                        service_type: this.serviceType,
                        channel_id: this.formData.channel_id,
                        profit_margin: this.formData.profit_margin,
                        details: this.formData.details,
                        variable_costs: this.formData.variable_costs,
                        _token: '{{ csrf_token() }}'
                    };
                    try {
                        const response = await fetch('{{ route("proposals.calculate") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json();
                        this.results = data;
                    } catch (error) { console.error(error); }
                },
                init() { this.calculate(); }
            }
        }
        function formatBRL(val) {
            if(!val && val !== 0) return '';
            return parseFloat(val).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        }
    </script>
</x-app-layout>