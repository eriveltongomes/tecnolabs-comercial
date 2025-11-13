<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nova Meta') }}</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900">
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <form method="POST" action="{{ route('settings.revenue-tiers.store') }}">
            @csrf
            <div><x-input-label for="name" :value="__('Nome da Meta (ex: Meta 1)')" /><x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus /></div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div><x-input-label for="min_value" :value="__('Valor Mínimo (R$)')" /><x-text-input id="min_value" class="block mt-1 w-full" type="number" step="0.01" name="min_value" :value="old('min_value')" required /></div>
                <div><x-input-label for="max_value" :value="__('Valor Máximo (R$)')" /><x-text-input id="max_value" class="block mt-1 w-full" type="number" step="0.01" name="max_value" :value="old('max_value')" required /></div>
            </div>
            <div class="flex items-center justify-end mt-4"><a href="{{ route('settings.revenue-tiers.index') }}" class="text-sm text-gray-600 mr-4">Cancelar</a><x-primary-button>{{ __('Salvar') }}</x-primary-button></div>
        </form>
    </div></div></div></div>
</x-app-layout>