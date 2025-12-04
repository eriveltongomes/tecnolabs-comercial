<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title ?? 'Relatório' }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <h3 class="text-xl font-bold text-gray-700">Em Desenvolvimento</h3>
                <p class="text-gray-500 mt-2">Este relatório estará disponível na próxima atualização.</p>
                <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Voltar ao Dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>