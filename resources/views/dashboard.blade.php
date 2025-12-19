<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1V10M9 20v-5a1 1 0 011-1h4a1 1 0 011 1v5M9 20h6"></path></svg>
            <span>{{ __('Dashboard') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h3 class="text-2xl font-bold text-gray-700">Olá, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-500">Aqui está o resumo das suas atividades.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                @if(in_array(Auth::user()->role, ['admin', 'financeiro']))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-gray-500 text-sm font-medium">Vendas Aprovadas (Este Mês)</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($data['total_sales'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="text-gray-500 text-sm font-medium">Comissões a Pagar (Este Mês)</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($data['total_commissions'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium">Propostas Pendentes</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">{{ $data['pending_count'] }}</div>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-gray-500 text-sm font-medium">Minhas Comissões (Este Mês)</div>
                        <div class="text-3xl font-bold text-green-600 mt-2">R$ {{ number_format($data['my_commissions'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-gray-500 text-sm font-medium">Total Vendido</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">R$ {{ number_format($data['my_sales'], 2, ',', '.') }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-500">
                        <div class="text-gray-500 text-sm font-medium">Propostas em Aberto</div>
                        <div class="text-3xl font-bold text-gray-800 mt-2">{{ $data['my_pending'] }}</div>
                    </div>
                @endif

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold text-gray-800">📈 Ritmo de Vendas (Burn-up)</h4>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Diário</span>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-center">
                    <h4 class="text-lg font-bold text-gray-800 mb-2">🎯 Meta do Mês</h4>
                    
                    <div class="text-center my-4">
                        <div class="text-4xl font-extrabold text-gray-900">
                            {{ number_format($data['goal_info']['percentage'], 1, ',', '.') }}%
                        </div>
                        <p class="text-sm text-gray-500">atingida</p>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-4 mb-4 dark:bg-gray-700">
                        <div class="h-4 rounded-full {{ $data['goal_info']['percentage'] >= 100 ? 'bg-green-500' : ($data['goal_info']['percentage'] > 50 ? 'bg-indigo-500' : 'bg-yellow-400') }}" 
                             style="width: {{ min($data['goal_info']['percentage'], 100) }}%"></div>
                    </div>

                    <div class="space-y-3 mt-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Realizado</span>
                            <span class="font-bold text-gray-800">R$ {{ number_format($data['goal_info']['achieved'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                            <span class="text-gray-500">Meta</span>
                            <span class="font-bold text-gray-800">R$ {{ number_format($data['goal_info']['target'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($data['goal_info']['remaining'] > 0)
                        <div class="mt-4 p-3 bg-red-50 text-red-700 rounded-md text-sm text-center">
                            Faltam <strong>R$ {{ number_format($data['goal_info']['remaining'], 2, ',', '.') }}</strong> para bater a meta! 🚀
                        </div>
                    @else
                        <div class="mt-4 p-3 bg-green-50 text-green-700 rounded-md text-sm text-center">
                            Parabéns! Meta batida! 🏆
                        </div>
                    @endif
                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-bold mb-4">Últimas Propostas</h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($data['recent_proposals'] as $prop)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ $prop->proposal_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $prop->client->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $prop->status === 'aprovada' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $prop->status === 'reprovada' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $prop->status === 'rascunho' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $prop->status === 'em_analise' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $prop->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">R$ {{ number_format($prop->total_value, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('proposals.show', $prop->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma movimentação recente.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="{{ route('proposals.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-bold">Ver todas as propostas &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            // Dados vindos do Controller (PHP -> JS)
            const labels = @json($data['chart']['labels']);
            const dataIdeal = @json($data['chart']['ideal']);
            const dataActual = @json($data['chart']['actual']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Realizado (Acumulado)',
                            data: dataActual,
                            borderColor: '#4F46E5', // Indigo 600
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Meta Ideal',
                            data: dataIdeal,
                            borderColor: '#9CA3AF', // Gray 400
                            borderDash: [5, 5], // Linha tracejada
                            borderWidth: 2,
                            pointRadius: 0, // Remove bolinhas da linha ideal
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>