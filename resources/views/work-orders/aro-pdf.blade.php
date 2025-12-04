<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ARO - Análise de Risco Operacional</title>
    <style>
        /** CONFIGURAÇÃO DA PÁGINA 
            Aumentamos o margin-top para 160px para "empurrar" o conteúdo para baixo 
            e dar espaço para o cabeçalho fixo não atropelar o texto.
        **/
        @page { margin: 160px 40px 80px 40px; }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            color: #000; 
        }
        
        /* CABEÇALHO FIXO */
        .header { 
            position: fixed; 
            top: -120px; /* Posição absoluta em relação à margem */
            left: 0px; 
            right: 0px; 
            height: 100px; /* Altura reservada */
            text-align: center; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px;
        }
        
        .logo { 
            max-height: 65px; 
            margin-bottom: 5px; 
            display: block; 
            margin-left: auto; 
            margin-right: auto; 
        }
        
        .header-title { 
            font-size: 18px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 5px;
        }
        
        /* RODAPÉ FIXO */
        .footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            text-align: center; 
            font-size: 9px; 
            color: #555; 
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

        /* CONTEÚDO */
        .content {
            /* O conteúdo flui naturalmente dentro das margens definidas no @page */
        }
        
        /* BOX DE INFORMAÇÕES */
        .info-box { 
            width: 100%; 
            margin-bottom: 20px; 
            border: 1px solid #000; 
            padding: 8px; 
            background-color: #fcfcfc;
        }
        .info-row { margin-bottom: 4px; }
        .label { font-weight: bold; width: 140px; display: inline-block; }

        /* TABELAS */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        
        /* Tabela de Equipamentos (Compacta) */
        .equip-table th { background-color: #eee; border: 1px solid #000; padding: 5px; text-align: left; font-size: 10px; }
        .equip-table td { border: 1px solid #000; padding: 5px; font-size: 10px; }

        /* Tabela ARO (Principal) */
        .aro-table th { background-color: #ccc; border: 1px solid #000; padding: 8px; text-align: left; }
        .aro-table td { border: 1px solid #000; padding: 8px; vertical-align: top; }
        
        /* STATUS GLOBAL */
        .risk-box {
            margin-bottom: 20px; 
            border: 2px solid #000; 
            padding: 10px; 
            background-color: #f0f0f0; 
            text-align: center;
        }
        
        /* Imagem Final */
        .risk-matrix-img { 
            width: 100%; 
            max-width: 650px; 
            margin: 20px auto; 
            display: block; 
            border: 1px solid #ccc; 
        }
        
        .signatures { margin-top: 40px; width: 100%; text-align: center; page-break-inside: avoid; }
        .sig-line { width: 250px; border-top: 1px solid #000; margin: 0 auto 5px auto; }
        
        /* Utilitários de Texto */
        .small-text { font-size: 9px; color: #555; display: block; margin-top: 2px; }
        .obs-text { font-size: 10px; font-style: italic; margin-top: 5px; display: block; border-top: 1px dotted #999; padding-top: 2px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <div class="header-title">Análise de Riscos Operacional (ARO)</div>
    </div>

    <div class="footer">
        OS #{{ $checklist->workOrder->id }} - Gerado em {{ date('d/m/Y H:i') }} - Tecnolabs
    </div>

    <div class="content">
        
        <div class="info-box">
            <div class="info-row"><span class="label">Empresa:</span> Tecnolabs - Agência Digital</div>
            <div class="info-row"><span class="label">Cliente:</span> {{ $checklist->workOrder->client->name }}</div>
            <div class="info-row"><span class="label">Local da Operação:</span> {{ $checklist->workOrder->service_location }}</div>
            <div class="info-row"><span class="label">Data da Operação:</span> {{ \Carbon\Carbon::parse($checklist->workOrder->scheduled_at)->format('d/m/Y') }}</div>
            <div class="info-row"><span class="label">Preenchido em:</span> {{ \Carbon\Carbon::parse($checklist->filled_at)->format('d/m/Y H:i') }}</div>
            
            <div class="info-row"><span class="label">Piloto Responsável:</span> {{ $checklist->user->name }}</div>
            <div class="info-row"><span class="label">ID DECEA:</span> {{ $checklist->user->decea_profile_id ?? 'Não Informado' }}</div>
            
            @if($checklist->workOrder->decea_protocol)
            <div class="info-row"><span class="label">Protocolo DECEA:</span> <strong>{{ $checklist->workOrder->decea_protocol }}</strong></div>
            @endif
        </div>

        @if($checklist->workOrder->equipments->count() > 0)
            <h3>Aeronaves / Equipamentos Utilizados</h3>
            <table class="equip-table">
                <thead>
                    <tr>
                        <th width="40%">Equipamento</th>
                        <th width="20%">Registro ANAC</th>
                        <th width="20%">Apólice de Seguro</th>
                        <th width="20%">Seguradora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checklist->workOrder->equipments as $eq)
                    <tr>
                        <td>{{ $eq->name }}</td>
                        <td>{{ $eq->anac_registration ?? '-' }}</td>
                        <td>{{ $eq->insurance_policy ?? '-' }}</td>
                        <td>{{ $eq->insurance_company ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="risk-box">
            RISCO GLOBAL DA OPERAÇÃO: 
            <span style="font-weight: bold; font-size: 16px; text-transform: uppercase; margin-left: 10px;">
                {{ $checklist->risk_level ?? 'NÃO AVALIADO' }}
            </span>
        </div>

        <h3>Matriz de Verificação</h3>
        <table class="aro-table">
            <thead>
                <tr>
                    <th width="35%">Situação / Risco</th>
                    <th width="30%">Análise Técnica</th>
                    <th width="35%">Medidas de Mitigação / Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checklist->answers as $answer)
                <tr>
                    <td>
                        <strong>{{ $answer->checklistItem->text ?? 'Item excluído' }}</strong>
                        @if(isset($answer->checklistItem->help_text))
                            <span class="small-text">{{ $answer->checklistItem->help_text }}</span>
                        @endif
                    </td>
                    
                    <td>
                        @if($answer->checklistItem && $answer->checklistItem->risk_level)
                            <div><strong>Probabilidade</strong> {{ $answer->checklistItem->probability }}</div>
                            <div><strong>Severidade</strong> {{ $answer->checklistItem->severity }}</div>
                            <div><strong>Risco</strong> {{ $answer->checklistItem->risk_level }}</div>
                            <div><strong>Tolerabilidade</strong> {{ $answer->checklistItem->tolerability }}</div>
                        @else
                            <span class="small-text">- Dados padrão -</span>
                        @endif
                    </td>

                    <td style="{{ $answer->is_ok ? 'background-color: #dff0d8;' : 'background-color: #f2dede;' }}">
                        <strong>{{ $answer->is_ok ? 'CONFORME' : 'NÃO CONFORME' }}</strong>
                        
                        @if($answer->checklistItem && $answer->checklistItem->mitigation)
                            <div class="small-text" style="margin-top: 5px;">
                                <strong>Mitigação:</strong> {{ $answer->checklistItem->mitigation }}
                            </div>
                        @endif
                        
                        @if($answer->observation)
                            <div class="obs-text">
                                <strong>Obs:</strong> {{ $answer->observation }}
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($checklist->comments)
        <div style="margin-top: 20px;">
            <strong>Observações Gerais:</strong><br>
            <div style="border: 1px solid #ccc; padding: 10px; background-color: #fff;">{{ $checklist->comments }}</div>
        </div>
        @endif

        <div class="signatures">
            <div class="sig-line"></div>
            <strong>{{ $checklist->user->name }}</strong><br>
            Piloto em Comando<br>
            ID DECEA: {{ $checklist->user->decea_profile_id ?? '-' }}
        </div>

        <div style="page-break-inside: avoid; margin-top: 40px; text-align: center;">
            <h3>Referência: Matriz de Risco</h3>
            <img src="{{ public_path('images/matriz_risco.png') }}" class="risk-matrix-img">
        </div>

    </div>
</body>
</html>