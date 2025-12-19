<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0h6m6 0v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
            <span>
                Meus Resultados e Performance
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <h3 class="text-3xl font-bold text-gray-800">Boas-vindas, {{ Auth::user()->name }}!</h3>
                <p class="text-lg text-gray-500">Aqui está o seu resumo de performance deste mês ({{ now()->locale('pt_BR')->translatedFormat('F') }}).</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Minhas Comissões (Mês)</div>
                    <div class="text-3xl font-bold text-green-600 mt-2">R$ {{ number_format($my_commissions, 2, ',', '.') }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Comissão Atual (por Canal)</div>
                    <div class="mt-2 space-y-1">
                        @foreach($currentPercentages as $data)
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm font-medium text-gray-700">{{ $data['channel_name'] }}:</span>
                            <span class="text-2xl font-bold text-indigo-600">{{ $data['percentage'] }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-500">
                    <div class="text-gray-500 text-sm font-medium">Propostas Em Aberto</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $my_pending }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Acompanhamento de Meta</h3>
                        <p class="text-sm text-gray-500">Faturamento (Mês):</p>
                        <p class="text-3xl font-bold text-indigo-600">R$ {{ number_format($my_sales, 2, ',', '.') }}</p>
                        
                        @if($nextTier)
                            <hr class="my-4">
                            <p class="text-sm text-gray-500">Próxima Meta:</p>
                            <p class="text-2xl font-bold text-gray-700">R$ {{ number_format($nextTier->min_value, 2, ',', '.') }}</p>
                            @php $falta = $nextTier->min_value - $my_sales; @endphp
                            <p class="text-sm text-green-600 font-bold">Faltam R$ {{ number_format($falta, 2, ',', '.') }} para atingir!</p>
                        @else
                            <p class="mt-4 text-green-600 font-bold">Parabéns, você atingiu a meta mais alta do mês!</p>
                        @endif
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Serviços Vendidos (Mês)</h3>
                        @php $serviceTranslations = ['drone' => 'Drone', 'timelapse' => 'Timelapse', 'tour_virtual' => 'Tour Virtual 360°']; @endphp
                        
                        @if($servicesSold->isEmpty())
                            <p class="text-gray-500">Nenhum serviço vendido este mês ainda.</p>
                        @else
                            <ul class="divide-y divide-gray-200">
                                @foreach($servicesSold as $serviceName => $data)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-md font-bold text-gray-800">{{ $serviceTranslations[$serviceName] ?? $serviceName }}</p>
                                        <p class="text-sm text-gray-500">{{ $data['count'] }} {{ $data['count'] > 1 ? 'vendas' : 'venda' }}</p>
                                    </div>
                                    <span class="text-lg font-bold text-gray-800">R$ {{ number_format($data['total'], 2, ',', '.') }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Propostas Aprovadas em {{ now()->translatedFormat('F') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Proposta</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Aprovada em</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Valor Venda</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase">Minha Comissão</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($myApprovedProposals as $proposal)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-indigo-600"><a href="{{ route('proposals.show', $proposal) }}">{{ $proposal->proposal_number }}</a></td>
                                    <td class="px-4 py-3 text-gray-500">{{ $proposal->client->name }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $proposal->approved_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-700">R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-green-600 font-bold">R$ {{ number_format($proposal->commission_value, 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Nenhuma proposta aprovada este mês.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Minhas Vendas (Últimos 12 Meses)</h3>
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Meus Canais (Este Mês)</h3>
                        <canvas id="channelSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico 1: Vendas Mensais (Linha) - AGORA TRADUZIDO
        const ctxMonthly = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlySales['labels']) !!}, // <-- Etiquetas agora vêm traduzidas
                datasets: [{
                    label: 'Faturamento Mensal',
                    data: {!! json_encode($monthlySales['data']) !!},
                    borderColor: 'rgb(79, 70, 229)', // Indigo
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.1
                }]
            },
        });

        // Gráfico 2: Vendas por Canal (Pizza)
        const ctxChannel = document.getElementById('channelSalesChart').getContext('2d');
        new Chart(ctxChannel, {
            type: 'pie',
            data: {
                labels: {!! json_encode($channelSales['labels']) !!},
                datasets: [{
                    label: 'Total Vendido',
                    data: {!! json_encode($channelSales['data']) !!},
                    backgroundColor: [
                        'rgba(29, 78, 216, 0.7)',  // Blue-700
                        'rgba(16, 185, 129, 0.7)', // Green-500
                        'rgba(245, 158, 11, 0.7)', // Amber-500
                        'rgba(107, 114, 128, 0.7)',// Gray-500
                        'rgba(192, 38, 211, 0.7)', // Fuchsia-600
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                }
            }
        });
    </script>
</x-app-layout>