<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>{{ __('Gestão Operacional') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('work-orders.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Nova OS Avulsa
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">OS #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente / Serviço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agendamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($workOrders as $os)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $os->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $os->status === 'pendente' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $os->status === 'agendada' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $os->status === 'em_execucao' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $os->status === 'concluida' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $os->status === 'cancelada' ? 'bg-gray-100 text-gray-600' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $os->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $os->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $os->client->name }}</div>
                                        @if($os->proposal_id)
                                            <div class="text-xs text-indigo-600">Via Proposta #{{ $os->proposal->proposal_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $os->scheduled_at ? $os->scheduled_at->format('d/m/Y H:i') : 'Não agendado' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $os->technician->name ?? '---' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        <a href="{{ route('work-orders.show', $os->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold mr-3" title="Ver Detalhes">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="{{ route('work-orders.edit', $os->id) }}" class="text-yellow-600 hover:text-yellow-900 font-bold mr-3" title="Gerenciar">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        
                                        @if(Auth::user()->role === 'admin')
                                            <form action="{{ route('work-orders.destroy', $os->id) }}" method="POST" class="inline-block" onsubmit="return confirm('ATENÇÃO: Excluir esta OS apagará todo o histórico de execução e checklists. Confirmar?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold" title="Excluir">
                                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Nenhuma ordem de serviço.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>