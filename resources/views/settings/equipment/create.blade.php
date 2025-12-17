<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Equipamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('settings.equipment.store') }}" x-data="{ type: 'drone' }">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tipo de Equipamento</label>
                                <select name="type" x-model="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                    <option value="drone">Drone (Aeronave)</option>
                                    <option value="camera">Câmera / Sensor</option>
                                    <option value="acessorio">Acessório</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nome / Modelo</label>
                                <input type="text" name="name" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Valor de Aquisição (R$)</label>
                                <input type="number" step="0.01" name="invested_value" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Vida Útil Estimada (Horas)</label>
                                <input type="number" name="lifespan_hours" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                <p class="text-xs text-gray-500 mt-1">Usado para cálculo de depreciação na proposta.</p>
                            </div>
                        </div>

                        <div x-show="type === 'drone'" class="mt-6 border-t pt-4">
                            <h3 class="font-bold text-gray-700 mb-4">Dados Legais e Regulatórios (ANAC)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Registro ANAC (SISANT)</label>
                                    <input type="text" name="anac_registration" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Nº da Apólice de Seguro (RETA)</label>
                                    <input type="text" name="insurance_policy" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Seguradora</label>
                                    <input type="text" name="insurance_company" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Validade do Seguro</label>
                                    <input type="date" name="insurance_expiry" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('settings.equipment.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 px-4 py-2 text-white">Salvar Equipamento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>