<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ARO - Análise de Risco Operacional</title>
    <style>
        /* MARGENS E FONTE */
        @page { margin: 140px 40px 60px 40px; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #000; line-height: 1.3; }
        
        /* CABEÇALHO */
        .header { position: fixed; top: -100px; left: 0px; right: 0px; height: 90px; text-align: center; border-bottom: 2px solid #333; padding-bottom: 5px; }
        .logo { max-height: 55px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto; }
        .header-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-top: 5px; }
        
        /* RODAPÉ */
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 20px; text-align: center; font-size: 8px; color: #777; border-top: 1px solid #ccc; padding-top: 5px; }

        /* UTILITÁRIOS */
        .page-break { page-break-after: always; }
        .no-break { page-break-inside: avoid; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        /* CAIXAS DE INFORMAÇÃO */
        .info-box { width: 100%; border: 1px solid #000; margin-bottom: 15px; padding: 5px; }
        .info-row { margin-bottom: 3px; }
        .label { font-weight: bold; width: 120px; display: inline-block; }

        /* TABELAS */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; }
        th { background-color: #e0e0e0; border: 1px solid #000; padding: 6px; text-align: left; font-weight: bold; }
        td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        
        /* CORES DE STATUS */
        .bg-green { background-color: #dff0d8; }
        .bg-red { background-color: #f2dede; }
        
        /* CORES DA MATRIZ */
        .mat-green { background-color: #92d050; color: #000; font-weight: bold; text-align: center; }
        .mat-yellow { background-color: #ffc000; color: #000; font-weight: bold; text-align: center; }
        .mat-orange { background-color: #e26b0a; color: #fff; font-weight: bold; text-align: center; }
        .mat-red { background-color: #c00000; color: #fff; font-weight: bold; text-align: center; }
        
        /* ESTILOS ESPECÍFICOS DA MATRIZ */
        .matrix-container { width: 100%; margin-top: 10px; }
        .matrix-table { width: 100%; border: 1px solid #000; }
        .matrix-table td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 8px; }
        .rotate { transform: rotate(-90deg); white-space: nowrap; height: 80px; display: block; width: 15px; margin: 0 auto; font-weight: bold; }

        /* TEXTOS EXPLICATIVOS */
        .text-block { font-size: 9px; margin-bottom: 10px; text-align: justify; }
        .text-title { font-weight: bold; text-decoration: underline; margin-bottom: 3px; display: block; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <div class="header-title">Análise de Riscos Operacional (ARO)</div>
    </div>

    <div class="footer">
        OS #{{ $checklist->workOrder->id }} - Documento gerado eletronicamente em {{ date('d/m/Y H:i') }} - Tecnolabs
    </div>

    <div class="content">
        
        <div class="info-box">
            <div class="info-row"><span class="label">Empresa:</span> Tecnolabs - Agência Digital</div>
            <div class="info-row"><span class="label">Cliente:</span> {{ $checklist->workOrder->client->name }}</div>
            <div class="info-row"><span class="label">Local da Operação:</span> {{ $checklist->workOrder->service_location }}</div>
            <div class="info-row"><span class="label">Data Prevista:</span> {{ \Carbon\Carbon::parse($checklist->workOrder->scheduled_at)->format('d/m/Y') }}</div>
            <div class="info-row"><span class="label">Realizado em:</span> {{ \Carbon\Carbon::parse($checklist->filled_at)->format('d/m/Y H:i') }}</div>
            <div class="info-row"><span class="label">Piloto Responsável:</span> {{ $checklist->user->name }}</div>
            <div class="info-row"><span class="label">ID DECEA:</span> {{ $checklist->user->decea_profile_id ?? '-' }}</div>
            @if($checklist->workOrder->decea_protocol)
            <div class="info-row"><span class="label">Protocolo DECEA:</span> <strong>{{ $checklist->workOrder->decea_protocol }}</strong></div>
            @endif
        </div>

        @if($checklist->workOrder->equipments->count() > 0)
            <div class="bold" style="margin-bottom: 5px;">Aeronaves / Equipamentos Utilizados</div>
            <table>
                <thead>
                    <tr>
                        <th width="35%">Equipamento</th>
                        <th width="20%">Registro ANAC</th>
                        <th width="25%">Apólice de Seguro</th>
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

        <div style="border: 1px solid #000; padding: 8px; margin-bottom: 20px; background-color: #eee;">
            <strong>RISCO GLOBAL DA OPERAÇÃO:</strong> 
            <span class="uppercase bold" style="margin-left: 10px; font-size: 12px;">{{ $checklist->risk_level ?? 'NÃO AVALIADO' }}</span>
        </div>

        <div class="bold" style="margin-bottom: 5px;">Matriz de Verificação e Mitigação</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">Situação / Risco</th>
                    <th width="25%">Análise Técnica</th>
                    <th width="45%">Medidas de Mitigação / Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checklist->answers as $answer)
                <tr>
                    <td>
                        <div class="bold">{{ $answer->checklistItem->text ?? 'Item excluído' }}</div>
                        @if(isset($answer->checklistItem->help_text))
                            <div style="font-size: 8px; color: #555; margin-top: 2px;">{{ $answer->checklistItem->help_text }}</div>
                        @endif
                    </td>
                    
                    <td>
                        @if($answer->checklistItem && $answer->checklistItem->risk_level)
                            <div><strong>Prob:</strong> {{ $answer->checklistItem->probability }}</div>
                            <div><strong>Sev:</strong> {{ $answer->checklistItem->severity }}</div>
                            <div><strong>Risco:</strong> {{ $answer->checklistItem->risk_level }}</div>
                            <div><strong>Toler:</strong> {{ $answer->checklistItem->tolerability }}</div>
                        @else
                            -
                        @endif
                    </td>

                    <td class="{{ $answer->is_ok ? 'bg-green' : 'bg-red' }}">
                        <div class="bold">{{ $answer->is_ok ? 'CONFORME' : 'NÃO CONFORME' }}</div>
                        
                        @if($answer->checklistItem && $answer->checklistItem->mitigation)
                            <div style="margin-top: 5px; font-style: italic;">
                                <strong>Mitigação:</strong> {{ $answer->checklistItem->mitigation }}
                            </div>
                        @endif
                        
                        @if($answer->observation)
                            <div style="margin-top: 5px; border-top: 1px dotted #999; padding-top: 2px;">
                                <strong>Obs:</strong> {{ $answer->observation }}
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($checklist->comments)
        <div style="margin-bottom: 20px;">
            <div class="bold">Observações Gerais:</div>
            <div style="border: 1px solid #ccc; padding: 5px; min-height: 30px;">{{ $checklist->comments }}</div>
        </div>
        @endif

        <div style="margin-top: 30px; width: 100%; text-align: center;">
            <div style="width: 200px; border-top: 1px solid #000; margin: 0 auto 5px auto;"></div>
            <strong>{{ $checklist->user->name }}</strong><br>
            Piloto em Comando
        </div>

        <div class="page-break"></div>
        
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" class="logo">
            <div class="header-title">Análise de Riscos Operacional (ARO)</div>
        </div>

        <div style="margin-top: 20px;">
            <h3>Referência: Matriz de Gerenciamento de Risco</h3>
            
            <div class="text-block">
                <span class="text-title">Probabilidade da ocorrência:</span>
                <p><strong>- Nível 5 (frequente):</strong> é provável que ocorra muitas vezes, ou historicamente tem ocorrido frequentemente;</p>
                <p><strong>- Nível 4 (ocasional):</strong> é provável que ocorra algumas vezes, ou historicamente tem ocorrido com pouca frequência;</p>
                <p><strong>- Nível 3 (remoto):</strong> é improvável, mas é possível que venha a ocorrer, ou ocorre raramente;</p>
                <p><strong>- Nível 2 (improvável):</strong> é bastante improvável que ocorra e não se tem notícia de que tenha alguma vez ocorrido;</p>
                <p><strong>- Nível 1 (muito improvável):</strong> é quase impossível que o evento ocorra.</p>
            </div>

            <div class="text-block">
                <span class="text-title">Severidade da ocorrência:</span>
                <p><strong>- Nível A (catastrófico):</strong> morte de múltiplas pessoas;</p>
                <p><strong>- Nível B (crítico):</strong> morte de pessoa, lesões gravíssimas, capazes de deixar sequelas significativas e/ou incapacitantes, tais como cegueira, paralisia, amputações, etc.;</p>
                <p><strong>- Nível C (significativo):</strong> lesões sérias a pessoas, mas não incapacitantes nem com sequelas significativas;</p>
                <p><strong>- Nível D (pequeno):</strong> incidentes menores, danos a objetos, animais ou vegetação no solo, lesões leves;</p>
                <p><strong>- Nível E (insignificante):</strong> somente danos ao equipamento.</p>
            </div>

            <div class="text-block">
                <span class="text-title">Tolerabilidade (Matriz):</span>
                <table class="matrix-table">
                    <tr>
                        <td rowspan="2" colspan="2" style="background-color: #ccc; font-weight: bold;">Severidade</td>
                        <td colspan="5" style="background-color: #ddd; font-weight: bold;">Nível</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td>Catastrófico (A)</td><td>Crítico (B)</td><td>Significativo (C)</td><td>Pequeno (D)</td><td>Insignificante (E)</td>
                    </tr>
                    <tr>
                        <td rowspan="5" style="background-color: #ddd;"><span class="rotate">Probabilidade</span></td>
                        <td style="font-weight: bold;">Frequente (5)</td><td class="mat-red">5A</td><td class="mat-red">5B</td><td class="mat-red">5C</td><td class="mat-orange">5D</td><td class="mat-yellow">5E</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Ocasional (4)</td><td class="mat-red">4A</td><td class="mat-red">4B</td><td class="mat-orange">4C</td><td class="mat-yellow">4D</td><td class="mat-green">4E</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Remoto (3)</td><td class="mat-red">3A</td><td class="mat-orange">3B</td><td class="mat-yellow">3C</td><td class="mat-green">3D</td><td class="mat-green">3E</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Improvável (2)</td><td class="mat-orange">2A</td><td class="mat-yellow">2B</td><td class="mat-green">2C</td><td class="mat-green">2D</td><td class="mat-green">2E</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Muito Improvável (1)</td><td class="mat-yellow">1A</td><td class="mat-green">1B</td><td class="mat-green">1C</td><td class="mat-green">1D</td><td class="mat-green">1E</td>
                    </tr>
                </table>
            </div>

            <div class="text-block">
                <p><strong>- <span style="color:#c00000">Risco Extremo (4A, 5A, 5B)</span>:</strong> A operação não deve ocorrer. Requer aprovação da presidência para exceções.</p>
                <p><strong>- <span style="color:#c00000">Alto Risco (3A, 4B, 5C)</span>:</strong> A operação não deveria ocorrer. Requer medidas mitigadoras e aprovação da diretoria.</p>
                <p><strong>- <span style="color:#e26b0a">Risco Moderado (1A, 2A, 2B, 3B, 3C, 4C, 4D, 5D, 5E)</span>:</strong> Operação requer controles preventivos e aprovação da chefia imediata.</p>
                <p><strong>- <span style="color:#ffc000">Baixo Risco (1B, 1C, 2C, 2D, 3D, 3E, 4E)</span>:</strong> Operação pode ocorrer. Controles opcionais.</p>
                <p><strong>- <span style="color:#00b050">Risco Muito Baixo (1D, 1E, 2E)</span>:</strong> Operação aceitável como concebida.</p>
            </div>
        </div>

    </div>
</body>
</html>