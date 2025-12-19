<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            🏆 Ranking de Vendas & Metas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col lg:flex-row items-center justify-between mb-8 gap-8">
                
                <form method="GET" action="{{ route('reports.team-ranking') }}" class="flex items-end gap-4 w-full lg:w-auto">
                    <div>
                        <label for="start_date" class="block text-xs font-bold text-gray-500 uppercase">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-bold text-gray-500 uppercase">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm shadow transition">
                        Filtrar
                    </button>
                </form>

                @php
                    // Cálculos visuais feitos na View para evitar mexer no Controller
                    $totalVendidoAbsoluto = $ranking->sum('total');
                    $faltaAbsoluto = max(0, $metaReferencia - $totalVendidoAbsoluto);
                    $porcentagemCapacidade = ($metaReferencia > 0 && isset($capacidadeEquipe)) ? ($capacidadeEquipe / $metaReferencia) * 100 : 0;
                    
                    // Cor dinâmica da barra
                    $barColor = 'bg-red-500';
                    if($porcentagemEmpresa >= 50) $barColor = 'bg-yellow-400';
                    if($porcentagemEmpresa >= 80) $barColor = 'bg-green-500';
                    if($porcentagemEmpresa >= 100) $barColor = 'bg-green-600';
                @endphp

                <div class="w-full lg:w-1/2 bg-gray-50 rounded-xl p-4 border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-indigo-100 rounded-full opacity-50 blur-xl"></div>

                    <div class="relative z-10">
                        <div class="flex justify-between items-end mb-2">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Meta Global da Empresa</span>
                                <div class="text-2xl font-black text-gray-800 flex items-baseline gap-2">
                                    {{ number_format($porcentagemEmpresa, 1, ',', '.') }}%
                                    <span class="text-xs font-medium text-gray-500">atingida</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs text-gray-500">Faltam para a meta</span>
                                <span class="text-lg font-bold text-red-500">R$ {{ number_format($faltaAbsoluto, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="relative w-full h-6 bg-gray-200 rounded-full overflow-visible mt-2 shadow-inner">
                            
                            <div class="{{ $barColor }} h-6 rounded-full transition-all duration-1000 flex items-center justify-end pr-2 shadow-sm text-white text-[10px] font-bold" 
                                 style="width: {{ min($porcentagemEmpresa, 100) }}%">
                                @if($porcentagemEmpresa > 10)
                                    R$ {{ number_format($totalVendidoAbsoluto, 0, ',', '.') }}
                                @endif
                            </div>

                            @if($porcentagemCapacidade > 0 && $porcentagemCapacidade < 100)
                                <div class="absolute top-0 bottom-0 border-r-2 border-dashed border-gray-400 z-20 opacity-60" 
                                     style="left: {{ $porcentagemCapacidade }}%;"
                                     title="Capacidade Teórica da Equipe: R$ {{ number_format($capacidadeEquipe, 2, ',', '.') }}">
                                </div>
                                <div class="absolute -bottom-5 text-[9px] text-gray-400 font-bold transform -translate-x-1/2" style="left: {{ $porcentagemCapacidade }}%;">
                                    Capacidade
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between mt-4 text-xs text-gray-500 border-t border-gray-200 pt-2">
                            <div class="flex flex-col">
                                <span class="uppercase font-bold text-[10px] text-gray-400">Realizado</span>
                                <span class="font-bold text-green-600">R$ {{ number_format($totalVendidoAbsoluto, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="uppercase font-bold text-[10px] text-gray-400">Meta Total</span>
                                <span class="font-bold text-indigo-900">R$ {{ number_format($metaReferencia, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-yellow-100 text-yellow-700 p-1 rounded">🏆</span> Podium (Top 3)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($ranking->take(3) as $index => $seller)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 relative flex flex-col h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1 {{ $seller->zerado ? 'opacity-80 grayscale' : '' }}">
                            
                            @if(!$seller->zerado)
                                <div class="absolute top-0 right-0 px-3 py-1 text-xs font-bold text-white rounded-bl-lg shadow-sm z-10
                                    {{ $index === 0 ? 'bg-yellow-400 shadow-yellow-200' : ($index === 1 ? 'bg-gray-400 shadow-gray-200' : 'bg-orange-400 shadow-orange-200') }} shadow-md">
                                    {{ $index === 0 ? '🏆 1º Ouro' : ($index === 1 ? '🥈 2º Prata' : '🥉 3º Bronze') }}
                                </div>
                            @endif

                            <div class="p-6 flex-grow">
                                <div class="flex items-start space-x-4 mb-6">
                                    <div class="flex-shrink-0">
                                        @if($seller->photo_url)
                                            <img class="h-14 w-14 rounded-full object-cover border-2 border-white shadow-md" src="{{ $seller->photo_url }}" alt="{{ $seller->name }}">
                                        @else
                                            <div class="h-14 w-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl font-bold border-2 border-white shadow-md">
                                                {{ $seller->avatar_initials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 leading-tight truncate">{{ $seller->name }}</h3>
                                        
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-wide border border-gray-200">
                                                {{ $seller->nome_meta }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $seller->status_color }}">
                                                {{ $seller->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mb-6">
                                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Total Confirmado</span>
                                    <span class="block text-3xl font-extrabold text-gray-800">
                                        R$ {{ number_format($seller->total, 2, ',', '.') }}
                                    </span>
                                    
                                    @if(!$isPeriodClosed && $seller->total > 0)
                                    <div class="mt-1 flex justify-center items-center text-xs text-indigo-600 font-medium" title="Projeção baseada no ritmo diário">
                                        🔮 Projeção: R$ {{ number_format($seller->projecao, 2, ',', '.') }}
                                    </div>
                                    @endif
                                </div>

                                <div class="mb-6">
                                    <div class="flex justify-between text-xs mb-1 font-bold">
                                        <span class="{{ $seller->atingiu_meta ? 'text-green-600' : 'text-blue-600' }}">
                                            {{ $seller->porcentagem }}% da Meta
                                        </span>
                                        @if($seller->falta > 0)
                                            <span class="text-red-400 font-normal">Falta: {{ number_format($seller->falta, 2, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full {{ $seller->atingiu_meta ? 'bg-green-500' : 'bg-blue-500' }}" 
                                             style="width: {{ $seller->porcentagem_visual }}%"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-2 border-t pt-4">
                                    <div class="text-center">
                                        <div class="text-[10px] text-gray-400 uppercase font-bold">Pipeline</div>
                                        <div class="text-sm font-bold text-blue-600 truncate" title="{{ number_format($seller->pipeline, 2, ',', '.') }}">
                                            {{ $seller->pipeline > 1000 ? number_format($seller->pipeline/1000, 1, ',', '.') . 'k' : $seller->pipeline }}
                                        </div>
                                    </div>
                                    <div class="text-center border-l border-r border-gray-100">
                                        <div class="text-[10px] text-gray-400 uppercase font-bold">Ticket Médio</div>
                                        <div class="text-sm font-bold text-gray-700">
                                            {{ $seller->ticket_medio > 1000 ? number_format($seller->ticket_medio/1000, 1, ',', '.') . 'k' : number_format($seller->ticket_medio, 0) }}
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-[10px] text-gray-400 uppercase font-bold">Conversão</div>
                                        <div class="text-sm font-bold {{ $seller->conversao >= 30 ? 'text-green-600' : 'text-gray-700' }}">
                                            {{ number_format($seller->conversao, 0) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-indigo-100 text-indigo-700 p-1 rounded">📋</span> Ranking Detalhado
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível / Meta</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Faturamento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progresso</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Médio</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Conversão</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pipeline</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ranking as $index => $seller)
                            <tr class="transition-colors {{ $loop->last ? 'bg-red-50 border-l-4 border-red-500' : 'hover:bg-gray-50' }}">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm
                                        {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'text-gray-500')) }}">
                                        {{ $index + 1 }}º
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($seller->photo_url)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $seller->photo_url }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                    {{ $seller->avatar_initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $seller->name }}</div>
                                            <div class="text-xs text-gray-500 {{ $seller->zerado ? 'text-red-400' : 'text-green-500' }}">
                                                {{ $seller->zerado ? 'Sem vendas' : 'Ativo' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $seller->nome_meta }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">R$ {{ number_format($seller->total, 2, ',', '.') }}</div>
                                    @if($seller->atingiu_meta)
                                        <span class="text-[10px] text-green-600 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            Meta Batida
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap align-middle">
                                    <div class="w-full max-w-[140px]">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold">{{ $seller->porcentagem }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $seller->porcentagem_visual }}%"></div>
                                        </div>
                                        @if($seller->falta > 0)
                                            <span class="text-[10px] text-red-400">Falta: {{ number_format($seller->falta, 2, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    R$ {{ number_format($seller->ticket_medio, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $seller->conversao >= 30 ? 'bg-green-100 text-green-800' : ($seller->conversao >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($seller->conversao, 1) }}%
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-blue-600">
                                    R$ {{ number_format($seller->pipeline, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        📈 Ritmo de Vendas (Diário)
                    </h3>
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">Top 5 Vendedores</span>
                </div>
                
                <div class="relative h-80 w-full">
                    <canvas id="rankingChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('rankingChart').getContext('2d');
            
            const labels = @json($chartCategories);
            const datasets = [];
            const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

            const topSellers = @json($topSellersChart);

            topSellers.forEach((seller, index) => {
                if(seller.total > 0) { 
                    datasets.push({
                        label: seller.name,
                        data: seller.daily_data,
                        borderColor: colors[index % colors.length],
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6
                    });
                }
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
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
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [2, 4],
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>