<x-mail::message>
# Nova Ordem de Serviço Pendente

Prezado Gestor Operacional,

Uma nova Ordem de Serviço foi criada e **aguarda a designação de técnico e agendamento** no sistema.

**Detalhes da OS:**
- **ID:** #{{ $workOrder->id }}
- **Cliente:** {{ $workOrder->client->name }}
- **Tipo de Serviço:** {{ ucfirst($workOrder->service_type) }}
- **Status Atual:** Pendente

Por favor, acesse o sistema para escalar um piloto e definir a data de execução.

<x-mail::button :url="route('work-orders.edit', $workOrder->id)">
Gerenciar Ordem de Serviço
</x-mail::button>

Obrigado,

{{ config('app.name') }}
</x-mail::message>