<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nova Regra de Comissão') }}</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900">
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <form method="POST" action="{{ route('settings.commission-rules.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="channel_id" :value="__('Canal de Prospecção')" />
                    <select name="channel_id" id="channel_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach($channels as $channel) <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>{{ $channel->name }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="revenue_tier_id" :value="__('Meta de Faturamento')" />
                    <select name="revenue_tier_id" id="revenue_tier_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach($tiers as $tier) <option value="{{ $tier->id }}" {{ old('revenue_tier_id') == $tier->id ? 'selected' : '' }}>{{ $tier->name }} ({{ $tier->min_value }} - {{ $tier->max_value }})</option> @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <x-input-label for="percentage" :value="__('Percentual de Comissão (%)')" />
                <x-text-input id="percentage" class="block mt-1 w-full" type="number" step="0.01" name="percentage" :value="old('percentage')" required />
            </div>
            <div class="flex items-center justify-end mt-4"><a href="{{ route('settings.commission-rules.index') }}" class="text-sm text-gray-600 mr-4">Cancelar</a><x-primary-button>{{ __('Salvar Regra') }}</x-primary-button></div>
        </form>
    </div></div></div></div>
</x-app-layout>