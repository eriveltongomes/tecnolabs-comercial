<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            🎯 Configuração de Metas da Empresa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-bold text-gray-700 mb-4">Definir Nova Meta</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Defina o valor global que a empresa deve buscar vender no mês. Se já existir uma meta para o mês escolhido, ela será atualizada.
                        </p>

                        <form action="{{ route('settings.monthly-goals.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="month" class="block text-sm font-medium text-gray-700">Mês</label>
                                <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->locale('pt_BR')->monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="year" class="block text-sm font-medium text-gray-700">Ano</label>
                                <input type="number" name="year" id="year" value="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="mb-6">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Valor da Meta (R$)</label>
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">R$</span>
                                    </div>
                                    <input type="text" name="amount" id="amount" 
                                           class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                           placeholder="0,00" 
                                           oninput="formatCurrency(this)"
                                           required>
                                </div>
                            </div>

                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Salvar Meta
                            </button>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-700">Histórico de Metas</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor da Meta</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($goals as $goal)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ ucfirst(\Carbon\Carbon::create()->month($goal->month)->locale('pt_BR')->monthName) }} / {{ $goal->year }}
                                                
                                                @if($goal->month == date('n') && $goal->year == date('Y'))
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Atual
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                R$ {{ number_format($goal->amount, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('settings.monthly-goals.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta meta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                                Nenhuma meta configurada ainda.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function formatCurrency(input) {
            let value = input.value;
            
            // Remove tudo que não é dígito
            value = value.replace(/\D/g, "");
            
            // Divide por 100 para ter os centavos
            value = (value / 100).toFixed(2) + "";
            
            // Troca ponto por vírgula
            value = value.replace(".", ",");
            
            // Adiciona pontos de milhar
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            
            input.value = value;
        }
    </script>
</x-app-layout>