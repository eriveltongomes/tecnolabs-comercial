<x-mail::message>
# Aprovação Financeira Necessária

Prezado time Financeiro,

Uma nova proposta comercial foi **ENVIADA PARA ANÁLISE** e requer sua aprovação.

**Detalhes da Proposta:**
- **Número:** #{{ $proposal->proposal_number }}
- **Cliente:** {{ $proposal->client->name }}
- **Valor Final:** R$ {{ $formattedValue }}  - **Vendedor:** {{ $proposal->user->name }}

Por favor, revise os custos e margens para dar o parecer final.

<x-mail::button :url="route('proposals.edit', $proposal->id)">
Ver Proposta
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>