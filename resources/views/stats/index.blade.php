<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6m6 0v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
            <span>
                {{ __('Meus Resultados e Performance') }}
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 text-sm font-medium">Taxa de Conversão (Aprovação)</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($conversionRate, 1) }}%</div>
                    <div class="text-xs text-gray-400 mt-1">De todas as propostas criadas, quantas viraram venda.</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">Ticket Médio</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">Valor médio de cada venda aprovada.</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Evolução de Vendas (6 Meses)</h3>
                    <canvas id="salesChart"></canvas>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Mix de Serviços</h3>
                    <div style="height: 300px; width: 300px; margin: 0 auto;">
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Dados do PHP para o JS
        const salesLabels = @json($salesData->pluck('month'));
        const salesValues = @json($salesData->pluck('total'));
        
        const serviceLabels = @json($serviceData->pluck('service_type'));
        const serviceValues = @json($serviceData->pluck('count'));

        // Gráfico de Vendas (Barra)
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Vendas (R$)',
                    data: salesValues,
                    backgroundColor: '#4F46E5',
                    borderRadius: 5
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Gráfico de Serviços (Doughnut)
        new Chart(document.getElementById('serviceChart'), {
            type: 'doughnut',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceValues,
                    backgroundColor: ['#10B981', '#F59E0B', '#3B82F6'],
                }]
            }
        });
    </script>
</x-app-layout>