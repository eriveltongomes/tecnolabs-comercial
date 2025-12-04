<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
            {{ __('Mapa de Status Operacional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <p class="text-gray-600">Panorama geral das {{ $total }} Ordens de Serviço registradas no sistema.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                
                <div class="bg-white p-4 rounded-lg shadow border-t-4 border-red-500">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-1">Pendentes</div>
                    <div class="text-3xl font-bold text-red-600">{{ $data['pendente'] }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $percentages['pendente'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $percentages['pendente'] }}%</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border-t-4 border-blue-500">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-1">Agendadas</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $data['agendada'] }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $percentages['agendada'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $percentages['agendada'] }}%</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border-t-4 border-yellow-500">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-1">Em Execução</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $data['em_execucao'] }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $percentages['em_execucao'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $percentages['em_execucao'] }}%</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border-t-4 border-green-500">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-1">Concluídas</div>
                    <div class="text-3xl font-bold text-green-600">{{ $data['concluida'] }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $percentages['concluida'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $percentages['concluida'] }}%</div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border-t-4 border-gray-400">
                    <div class="text-gray-500 text-xs font-bold uppercase mb-1">Canceladas</div>
                    <div class="text-3xl font-bold text-gray-600">{{ $data['cancelada'] }}</div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-gray-400 h-1.5 rounded-full" style="width: {{ $percentages['cancelada'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $percentages['cancelada'] }}%</div>
                </div>

            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-6">
                <h3 class="font-bold text-indigo-900 mb-2">Sugestão de Análise Rápida</h3>
                <ul class="list-disc list-inside text-sm text-indigo-800 space-y-1">
                    <li>Se houver muitas OSs <strong>Pendentes</strong>, verifique se o Gestor está agendando a equipe corretamente.</li>
                    <li>Muitas OSs <strong>Em Execução</strong> por longo tempo podem indicar esquecimento de "Finalizar" por parte dos pilotos.</li>
                    <li>Um alto número de <strong>Canceladas</strong> pode indicar falha no processo de venda ou agendamento.</li>
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>