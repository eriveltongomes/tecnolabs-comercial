<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Custo Fixo') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('settings.fixed-costs.update', $fixedCost->id) }}">
                        @csrf @method('PATCH')
                        <div>
                            <x-input-label for="name" :value="__('Descrição')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $fixedCost->name)" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="monthly_value" :value="__('Valor Mensal (R$)')" />
                            <x-text-input id="monthly_value" class="block mt-1 w-full" type="number" step="0.01" name="monthly_value" :value="old('monthly_value', $fixedCost->monthly_value)" required />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('settings.fixed-costs.index') }}" class="text-sm text-gray-600 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Atualizar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>