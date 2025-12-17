<x-mail::message>
# Você foi escalado para uma Missão!

Prezado(a) Piloto(a) {{ $workOrder->technician->name ?? 'Técnico(a)' }},

Você foi designado para a seguinte Ordem de Serviço:

**Detalhes da OS:**
- **ID:** #{{ $workOrder->id }}
- **Cliente:** {{ $workOrder->client->name }}
- **Local:** {{ $workOrder->service_location }}
- **Agendamento:** {{ $workOrder->scheduled_at ? $workOrder->scheduled_at->format('d/m/Y \à\s H:i') : 'A DEFINIR' }}

O PDF da Ordem de Serviço completa e o Escopo estão anexados neste e-mail. Por favor, revise os dados antes de iniciar o serviço no campo.

<x-mail::button :url="route('work-orders.show', $workOrder->id)">
Ver Checklists no App
</x-mail::button>

Bom voo!

{{ config('app.name') }}
</x-mail::message>