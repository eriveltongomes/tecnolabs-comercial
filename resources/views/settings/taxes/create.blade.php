<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Novo Imposto') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('settings.taxes.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Nome do Imposto (ex: ISS)')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="percentage" :value="__('Percentual (%)')" />
                            <x-text-input id="percentage" class="block mt-1 w-full" type="number" step="0.01" name="percentage" :value="old('percentage')" required />
                        </div>
                        <div class="block mt-4">
                            <label for="is_default" class="inline-flex items-center">
                                <input id="is_default" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_default">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Aplicar este imposto automaticamente em todas as propostas?') }}</span>
                            </label>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('settings.taxes.index') }}" class="text-sm text-gray-600 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>