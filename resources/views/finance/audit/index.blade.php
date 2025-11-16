@php
// --- DICIONÁRIO DE TRADUÇÃO DA AUDITORIA ---

// Para a coluna "Ação" (tradução dos padrões)
$actionTranslations = [
    'created' => 'Criado',
    'updated' => 'Atualizado',
    'deleted' => 'Deletado',
];

// Para a coluna "Alvo" (tradução dos Models)
$subjectTranslations = [
    'Proposal' => 'Proposta',
    'Client' => 'Cliente',
    'User' => 'Usuário',
    'Equipment' => 'Equipamento',
    'Course' => 'Curso',
    'Tax' => 'Imposto',
    'FixedCost' => 'Custo Fixo',
    'Channel' => 'Canal',
    'RevenueTier' => 'Meta (Faixa)',
    'CommissionRule' => 'Regra de Comissão',
];

// Para a coluna "Detalhes" (tradução dos campos do banco)
$fieldTranslations = [
    'status' => 'Status',
    'total_value' => 'Valor Total',
    'commission_value' => 'Comissão',
    'rejection_reason' => 'Motivo (Reprovação)',
    'motivo' => 'Motivo (Estorno)',
    'name' => 'Nome',
    'email' => 'E-mail',
    'role' => 'Perfil',
    'percentage' => 'Percentual',
    'invested_value' => 'Valor Investido',
    'lifespan_hours' => 'Vida Útil (Horas)',
    'monthly_value' => 'Valor Mensal',
    // Adicione mais campos aqui se necessário
];

@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auditoria e Logs do Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Data</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Usuário</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Ação</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Alvo</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Detalhes (De -> Para)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($activities as $log)
                                <tr class="{{ str_contains($log->description, 'ESTORNO') ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    
                                    <td class="px-6 py-4 font-bold text-gray-700">{{ $log->causer->name ?? 'Sistema' }}</td>
                                    
                                    <td class="px-6 py-4">
                                        @php
                                            // Se for um log padrão (created/updated), traduz. Se for customizado (log('Aprovou...')), usa o customizado.
                                            $actionText = $actionTranslations[$log->description] ?? $log->description;
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-bold 
                                            {{ $log->description == 'created' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $log->description == 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ str_contains($log->description, 'ESTORNO') ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $actionText }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-gray-500">
                                        @php
                                            $subjectName = class_basename($log->subject_type);
                                            $translatedSubject = $subjectTranslations[$subjectName] ?? $subjectName;
                                        @endphp
                                        {{ $translatedSubject }} #{{ $log->subject_id }}
                                    </td>

                                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">
                                        @if($log->properties->has('motivo'))
                                            <div class="text-red-600 mb-1"><strong>{{ $fieldTranslations['motivo'] ?? 'Motivo' }}:</strong> {{ $log->properties['motivo'] }}</div>
                                        @endif
                                        
                                        @if($log->properties->has('attributes') || $log->properties->has('old'))
                                            @php
                                                $attributes = $log->properties->get('attributes', []);
                                                $old = $log->properties->get('old', []);
                                            @endphp
                                            
                                            @foreach($attributes as $key => $newValue)
                                                @if($key != 'updated_at')
                                                    @php
                                                        $translatedKey = $fieldTranslations[$key] ?? $key;
                                                        $oldValue = $old[$key] ?? 'N/A';
                                                    @endphp
                                                    <div class="truncate w-64" title="De: {{ $oldValue }} | Para: {{ $newValue }}">
                                                        <strong>{{ $translatedKey }}:</strong> 
                                                        <span class="text-gray-400" style="text-decoration: line-through;">{{ $oldValue }}</span> → <span class="text-black">{{ $newValue }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="px-6 py-4 text-center">Nenhum registro de auditoria encontrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <div class="mt-4">
                            {{ $activities->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>