<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>{{ __('Meus Serviços Agendados') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($workOrders->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <p>Nenhum serviço agendado para você no momento.</p>
                    <p class="text-sm mt-2">Aproveite o descanso!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($workOrders as $os)
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 {{ $os->status == 'em_execucao' ? 'border-green-500' : 'border-blue-500' }}">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-2 py-1 text-xs font-bold rounded uppercase {{ $os->status == 'em_execucao' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ str_replace('_', ' ', $os->status) }}
                                </span>
                                <span class="text-sm text-gray-500 font-bold">OS #{{ $os->id }}</span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $os->title }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $os->client->name }}</p>
                            
                            <div class="space-y-2 text-sm text-gray-700 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $os->scheduled_at->format('d/m/Y \à\s H:i') }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $os->service_location }}
                                </div>
                            </div>

                            <a href="{{ route('work-orders.execute', $os->id) }}" class="block w-full text-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow transition duration-150">
                                Acessar Serviço
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>