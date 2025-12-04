<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Configurações: Equipamentos') }}
            </h2>
            <a href="{{ route('settings.index') }}" class="text-gray-600 hover:text-gray-900">Voltar</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4 text-right">
                        <a href="{{ route('settings.equipment.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Novo Equipamento
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Investido (R$)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vida Útil (Horas)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Custo/Hora (Calc)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($equipments as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ {{ number_format($item->invested_value, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->lifespan_hours }} h</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                        R$ {{ $item->lifespan_hours > 0 ? number_format($item->invested_value / $item->lifespan_hours, 2, ',', '.') : '0,00' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        <a href="{{ route('settings.equipment.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                        <form action="{{ route('settings.equipment.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Excluir este item?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum registro encontrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>