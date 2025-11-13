<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configurações do Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Aqui você pode gerenciar todas as regras de negócio do sistema. Os CRUDs completos para cada seção serão implementados na sequência.</p>
                    
                    <h3 class="text-lg font-semibold mb-2">Parâmetros de Cálculo</h3>
                    <ul class="list-disc list-inside space-y-2">
                        <li><a href="{{ route('settings.equipment.index') }}">Equipamentos</a></li>
                        <li><a href="{{ route('settings.courses.index') }}">Cursos</a></li>
                        <li><a href="{{ route('settings.taxes.index') }}">Impostos</a></li>
                        <li><a href="{{ route('settings.fixed-costs.index') }}">Custos Fixos</a></li>
                    </ul>

                    <h3 class="text-lg font-semibold mt-6 mb-2">Parâmetros de Comissão (A Matriz)</h3>
                    <ul class="list-disc list-inside space-y-2">
                        <li><a href="{{ route('settings.channels.index') }}">Canais de Prospecção</a></li>
                        <li><a href="{{ route('settings.revenue-tiers.index') }}">Metas de Faturamento</a></li>
                        <li><a href="{{ route('settings.commission-rules.index') }}">Regras de Comissão (Matriz)</a></li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>