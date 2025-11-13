<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Configurações: Canais de Prospecção') }}</h2>
            <a href="{{ route('settings.index') }}" class="text-gray-600 hover:text-gray-900">Voltar</a>
        </div>
    </x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900">
        <div class="mb-4 text-right">
            <a href="{{ route('settings.channels.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">Novo Canal</a>
        </div>
        @if(session('success'))<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>@endif
        <table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome do Canal</th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th></tr></thead><tbody class="bg-white divide-y divide-gray-200">
            @forelse ($channels as $channel)
            <tr><td class="px-6 py-4 text-sm text-gray-900">{{ $channel->name }}</td><td class="px-6 py-4 text-sm text-right"><a href="{{ route('settings.channels.edit', $channel->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a><form action="{{ route('settings.channels.destroy', $channel->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Excluir?');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900">Excluir</button></form></td></tr>
            @empty <tr><td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Nenhum canal cadastrado.</td></tr> @endforelse
        </tbody></table>
    </div></div></div></div>
</x-app-layout>