<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ordem de Serviço #{{ $workOrder->id }}</title>
    <style>
        /* Aumentei a margem do topo para 150px para dar espaço ao cabeçalho maior */
        @page { margin: 150px 40px 60px 40px; }
        
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #000; line-height: 1.3; }
        
        /* CABEÇALHO FIXO */
        .header { 
            position: fixed; 
            top: -110px; /* Ajustado para alinhar com a nova margem */
            left: 0px; 
            right: 0px; 
            height: 100px; /* Altura aumentada para caber Logo + Título sem cortar */
            text-align: center; 
            border-bottom: 2px solid #333; 
            padding-bottom: 5px; 
        }
        
        .logo { 
            max-height: 55px; 
            margin-bottom: 5px; 
            display: block; 
            margin-left: auto; 
            margin-right: auto; 
        }
        
        .header-title { 
            font-size: 18px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 8px; /* Espaço entre logo e título */
        }
        
        /* RODAPÉ FIXO */
        .footer { 
            position: fixed; 
            bottom: -30px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            text-align: center; 
            font-size: 9px; 
            color: #555; 
            border-top: 1px solid #ccc; 
            padding-top: 5px; 
        }

        /* TÍTULOS DE SEÇÃO */
        h2 { 
            font-size: 13px; 
            background-color: #eee; 
            padding: 6px; 
            border-bottom: 1px solid #aaa; 
            margin-top: 20px; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
            font-weight: bold;
        }

        /* TABELAS */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background-color: #f0f0f0; border: 1px solid #999; padding: 6px; text-align: left; font-weight: bold; font-size: 10px; }
        td { border: 1px solid #999; padding: 6px; vertical-align: top; font-size: 10px; }

        /* GRID DE INFORMAÇÕES (Sem bordas visíveis) */
        .info-grid { width: 100%; margin-bottom: 15px; }
        .info-cell { padding: 5px; vertical-align: top; }
        .label { font-weight: bold; color: #444; display: block; font-size: 9px; text-transform: uppercase; margin-bottom: 2px; }
        .value { font-size: 12px; color: #000; }

        /* CAIXA DE DESCRIÇÃO */
        .description-box { 
            border: 1px solid #999; 
            padding: 10px; 
            min-height: 80px; 
            background-color: #fdfdfd; 
            text-align: justify; 
        }
        
        /* ASSINATURAS */
        .signatures { margin-top: 80px; width: 100%; }
        .sig-block { float: left; width: 45%; text-align: center; page-break-inside: avoid; }
        .sig-line { width: 80%; border-top: 1px solid #000; margin: 0 auto 5px auto; }
        
        .status-badge { 
            font-weight: bold; 
            text-transform: uppercase; 
            padding: 2px 5px; 
            border: 1px solid #000; 
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <div class="header-title">Ordem de Serviço (OS) #{{ $workOrder->id }}</div>
    </div>

    <div class="footer">
        Tecnolabs - Sistema Comercial e Operacional - Gerado em {{ date('d/m/Y H:i') }}
    </div>

    <div class="content">
        
        <table class="info-grid" style="border: none;">
            <tr>
                <td style="border: none; width: 50%;" class="info-cell">
                    <span class="label">Cliente</span>
                    <div class="value">{{ $workOrder->client->name }}</div>
                </td>
                <td style="border: none; width: 50%;" class="info-cell">
                    <span class="label">Local da Operação</span>
                    <div class="value">{{ $workOrder->service_location }}</div>
                </td>
            </tr>
            <tr>
                <td style="border: none;" class="info-cell">
                    <span class="label">Data Agendada</span>
                    <div class="value">{{ $workOrder->scheduled_at ? $workOrder->scheduled_at->format('d/m/Y H:i') : 'A definir' }}</div>
                </td>
                <td style="border: none;" class="info-cell">
                    <span class="label">Status Atual</span>
                    <div class="value">
                        <span class="status-badge">{{ str_replace('_', ' ', $workOrder->status) }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="border: none;" class="info-cell">
                    <span class="label">Técnico Responsável</span>
                    <div class="value">{{ $workOrder->technician->name ?? 'Não atribuído' }}</div>
                </td>
                <td style="border: none;" class="info-cell">
                    <span class="label">Tipo de Serviço</span>
                    <div class="value">{{ ucfirst($workOrder->service_type) }}</div>
                </td>
            </tr>
        </table>

        <h2>Descrição do Serviço (Escopo)</h2>
        <div class="description-box">
            {!! nl2br(e($workOrder->description)) !!}
        </div>

        <h2>Dados Regulatórios e Operacionais</h2>
        <table>
            <tr>
                <th width="50%">Protocolo DECEA / SARPAS</th>
                <th width="50%">Altura Máxima Autorizada (AGL)</th>
            </tr>
            <tr>
                <td>{{ $workOrder->decea_protocol ?? 'Não informado' }}</td>
                <td>{{ $workOrder->flight_max_altitude ? $workOrder->flight_max_altitude . ' metros' : 'Padrão (120m)' }}</td>
            </tr>
        </table>

        <h2>Equipamentos Alocados</h2>
        @if($workOrder->equipments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="40%">Equipamento</th>
                        <th width="25%">Registro ANAC</th>
                        <th width="35%">Seguro (Apólice)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrder->equipments as $eq)
                    <tr>
                        <td>{{ $eq->name }}</td>
                        <td>{{ $eq->anac_registration ?? '-' }}</td>
                        <td>{{ $eq->insurance_policy ?? '-' }} ({{ $eq->insurance_company ?? '' }})</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 10px; border: 1px dashed #ccc; color: #777; text-align: center; font-size: 10px;">Nenhum equipamento específico vinculado a esta OS.</div>
        @endif

        <h2>Controle de Segurança e Qualidade</h2>
        <table>
            <thead>
                <tr>
                    <th width="60%">Documento / Checklist</th>
                    <th width="20%">Status</th>
                    <th width="20%">Preenchido Por</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workOrder->checklists as $ck)
                <tr>
                    <td>{{ $ck->checklistModel->name }}</td>
                    <td style="text-align: center; font-weight: bold; color: {{ $ck->filled_at ? 'green' : 'red' }};">
                        {{ $ck->filled_at ? 'REALIZADO' : 'PENDENTE' }}
                    </td>
                    <td style="text-align: center;">{{ $ck->user->name ?? '-' }}</td>
                </tr>
                @endforeach
                @if($workOrder->checklists->isEmpty())
                    <tr><td colspan="3" style="text-align: center; font-style: italic;">Nenhum checklist obrigatório vinculado.</td></tr>
                @endif
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <strong>{{ $workOrder->technician->name ?? 'Técnico Responsável' }}</strong><br>
                Tecnolabs
            </div>
            <div class="sig-block" style="float: right;">
                <div class="sig-line"></div>
                <strong>{{ $workOrder->client->contact_name ?? 'Cliente' }}</strong><br>
                Aceite do Serviço / De Acordo
            </div>
        </div>

    </div>
</body>
</html>