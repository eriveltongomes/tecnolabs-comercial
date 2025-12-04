<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Modelos de Checklist / ARO') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('checklist-models.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700">
                            Criar Novo Modelo
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qtd Perguntas</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($models as $model)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $model->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($model->type == 'aro') <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">ARO (Segurança)</span>
                                        @elseif($model->type == 'pre_voo') <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Pré-Voo</span>
                                        @else <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($model->type) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $model->items_count }} itens</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('checklist-models.edit', $model->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">Editar</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum modelo cadastrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>