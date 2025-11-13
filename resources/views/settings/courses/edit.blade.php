<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Curso') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('settings.courses.update', $course->id) }}">
                        @csrf @method('PATCH')
                        <div>
                            <x-input-label for="name" :value="__('Nome do Curso')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $course->name)" required autofocus />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="invested_value" :value="__('Valor Investido (R$)')" />
                            <x-text-input id="invested_value" class="block mt-1 w-full" type="number" step="0.01" name="invested_value" :value="old('invested_value', $course->invested_value)" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="lifespan_hours" :value="__('Validade (em Horas de Voo)')" />
                            <x-text-input id="lifespan_hours" class="block mt-1 w-full" type="number" name="lifespan_hours" :value="old('lifespan_hours', $course->lifespan_hours)" required />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('settings.courses.index') }}" class="text-sm text-gray-600 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Atualizar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>