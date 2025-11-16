<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Proposta Comercial {{ $proposal->proposal_number }}</title>
    <style>
        /* MARGEM SUPERIOR AUMENTADA (160px):
           Isso empurra o texto para baixo, abrindo espaço para o Banner.
        */
        @page { margin: 160px 50px 80px 50px; }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11pt; 
            line-height: 1.5; 
            color: #333; 
            text-align: justify;
        }
        
        /* CABEÇALHO (Banner Full Width) */
        .header { 
            position: fixed; 
            top: -140px; /* Posiciona bem no topo da folha */
            left: 0px; 
            right: 0px; 
            height: 130px; /* Altura reservada para a imagem */
            text-align: center; 
            /* border-bottom: 1px solid #ccc; (Opcional: linha fina abaixo do banner) */
        }
        
        /* A MÁGICA DO BANNER */
        .logo { 
            width: 100%;       /* Força a largura total */
            height: auto;      /* Mantém a proporção */
            max-height: 130px; /* Garante que não invada o texto se for muito alta */
            object-fit: contain;
        }
        
        /* Rodapé */
        .footer { position: fixed; bottom: -40px; left: 0px; right: 0px; height: 30px; text-align: center; font-size: 9pt; color: #777; border-top: 1px solid #ccc; padding-top: 10px; }

        /* Conteúdo */
        .content { margin-top: 10px; }
        
        .date-right { text-align: right; margin-bottom: 30px; font-size: 11pt; }
        .recipient { margin-bottom: 30px; font-weight: bold; }
        
        /* Títulos */
        h1 { 
            font-size: 12pt; 
            color: #000; 
            text-transform: uppercase; 
            font-weight: bold;
            margin-top: 25px; 
            margin-bottom: 10px; 
        }
        
        ul { margin-bottom: 15px; padding-left: 20px; }
        li { margin-bottom: 5px; }

        /* Tabelas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #e0e0e0; border: 1px solid #000; padding: 8px; font-weight: bold; text-align: center; font-size: 10pt; }
        td { border: 1px solid #000; padding: 8px; text-align: center; font-size: 10pt; }
        .text-left { text-align: left; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        
        /* Tabela de Homologações (estilo limpo) */
        .homolog-table th { background-color: #fff; border: 1px solid #000; vertical-align: top; }
        .homolog-table td { vertical-align: top; font-size: 9pt; text-align: left; }

        .signature { margin-top: 60px; text-align: center; font-size: 10pt; page-break-inside: avoid; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/labs_proposta.jpg') }}" class="logo" alt="Tecnolabs">
    </div>

    <div class="footer">
        Tecnolabs - Agência Digital • CNPJ: 44.310.501/0001-40 • www.tecnolabs.info
    </div>

    <div class="content">
        
        <div class="date-right">
            Maceió, {{ \Carbon\Carbon::parse($proposal->created_at)->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') }}
        </div>

        <div class="recipient">
            Aos cuidados de {{ $proposal->client->contact_name ?? 'Responsável' }}<br>
            Empresa: {{ $proposal->client->name }}
        </div>

        <p>Primeiramente, gostaríamos de agradecer a oportunidade que nos foi concedida para demonstrar nossos serviços. Esta proposta abrange o nosso ecossistema de soluções tecnológicas, que inclui: <b>Serviços com Drone</b>, <b>Mapeamento Aéreo Georreferenciado</b>, <b>Tour Virtual 360°</b> e <b>Timelapse de Obra</b>, garantindo inovação e precisão para todas as etapas do seu projeto.</p>
        <p>Certos de estarmos apresentando uma alternativa de conquista e fidelização de vocês clientes e atendendo suas necessidades, estamos à disposição para outros esclarecimentos.</p>

        <h1>1. Quem Somos</h1>
        <p>A Tecnolabs - Agência Digital oferece serviços de imagens aéreas, tour virtual 360° e timelapse em alta qualidade e profissionalismo.</p>
        <p>Entender a necessidade do cliente é a primeira exigência para decolar com segurança e precisão, oferecemos serviços executados por profissionais que visam entender o seu negócio.</p>
        <p>Não somos apenas pilotos, mas também consultores de produtividade, onde procuramos entregar o melhor dos nossos serviços.</p>

        <h1>2. Serviço</h1>
        <div style="margin-bottom: 10px;">
            {!! nl2br(e($proposal->scope_description)) !!}
        </div>
        
        <p>Vale ressaltar que as inserções de elementos visuais e anonimização de dados pessoais serão aplicados em todas as imagens.</p>
        
        @if($proposal->service_type == 'drone' || $proposal->service_type == 'tour_virtual')
        <ul>
            <li><strong>Vídeos:</strong> serão entregues no mesmo formato que foram captados (4K) juntamente com tela de abertura/encerramento, ajuste de cores, decupagem, transição e música de fundo.</li>
            <li><strong>Fotos:</strong> serão entregues no mesmo formato que foram captadas (FullHD) com ajustes e correção de cores e remoção de interferências.</li>
        </ul>
        @endif

        <h1>3. Entrega do Material</h1>
        <p>Todas as fotos e vídeos brutos serão entregues no mesmo dia, se houver um computador e pendrive para a transferência dos arquivos.</p>
        <p>Caso o cliente não disponha de um computador no dia, os arquivos serão disponibilizados através de um serviço de nuvem que será fornecido o link com senha (nuvem) para a visualização e download.</p>
        <ul>
            <li><strong>Material Bruto:</strong> Disponibilizado até 48hrs após a captação.</li>
            <li><strong>Material Editado:</strong> Disponibilizado até 72hrs após aprovação das imagens.</li>
            <li><strong>Permanência:</strong> Link ficará disponível por até 30 (trinta) dias.</li>
        </ul>

        <h1>4. Investimento</h1>
        
        @if($proposal->service_type == 'timelapse')
            <table>
                <thead>
                    <tr>
                        <th width="40%">DESCRIÇÃO</th>
                        <th>Valor Unit.</th>
                        <th>Qtd/Período</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left">
                            <strong>TIMELAPSE COM PAINEL SOLAR</strong><br>
                            <small>Pagamento mensal</small>
                        </td>
                        <td>R$ {{ number_format($proposal->service_details['monthly_cost'], 2, ',', '.') }}</td>
                        <td>{{ $proposal->service_details['months'] }} Meses</td>
                        <td>R$ {{ number_format($proposal->service_details['monthly_cost'] * $proposal->service_details['months'], 2, ',', '.') }}</td>
                    </tr>
                    @if(!empty($proposal->service_details['installation_cost']))
                    <tr>
                        <td class="text-left">INSTALAÇÃO DA CÂMERA (Setup)</td>
                        <td>R$ {{ number_format($proposal->service_details['installation_cost'], 2, ',', '.') }}</td>
                        <td>1</td>
                        <td>R$ {{ number_format($proposal->service_details['installation_cost'], 2, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($proposal->courtesy)
                    <tr>
                        <td class="text-left"><strong>CORTESIA / BÔNUS</strong></td>
                        <td colspan="2" class="text-left">{{ $proposal->courtesy }}</td>
                        <td><strong>R$ 0,00</strong></td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="text-left" colspan="3">TOTAL GERAL</td>
                        <td>R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <table>
                <thead>
                    <tr>
                        <th width="50%">DESCRIÇÃO</th>
                        <th>Preço Unitário</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left">
                            <strong>CAPTAÇÃO DE IMAGENS ({{ ucfirst($proposal->service_type) }})</strong>
                        </td>
                        <td>R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                    </tr>
                    @if($proposal->courtesy)
                    <tr>
                        <td class="text-left"><strong>CORTESIA</strong></td>
                        <td colspan="2"><strong>{{ $proposal->courtesy }}</strong></td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="text-left" colspan="2">Total Geral</td>
                        <td>R$ {{ number_format($proposal->total_value, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        <p><strong>Taxas e Impostos:</strong> Todos os impostos incidentes e de serviços (ISS), o transporte e o seguro estão inclusos no preço.</p>

        <h1>5. Dados de Execução</h1>
        <p><strong>Local do Serviço:</strong> {{ $proposal->service_location }}</p>
        <p><strong>Data Prevista:</strong> {{ \Carbon\Carbon::parse($proposal->service_date)->format('d/m/Y') }}</p>
        <p><strong>Condições de Pagamento:</strong> {{ $proposal->payment_terms }}</p>

        <h1>6. Homologações e Seguros</h1>
        <table class="homolog-table">
            <tr>
                <th width="33%" style="text-align: center;">
                    <img src="{{ public_path('images/anatel.png') }}" style="max-height: 70px;"><br>
                </th>
                <th width="33%" style="text-align: center;">
                    <img src="{{ public_path('images/anac.png') }}" style="max-height: 70px;"><br>
                </th>
                <th width="33%" style="text-align: center;">
                    <img src="{{ public_path('images/decea.png') }}" style="max-height: 70px;"><br>
                </th>
            </tr>
            <tr>
                <td>A Tecnolabs possui drones homologados pela ANATEL.</td>
                <td>Aeronaves cadastradas na ANAC conforme regulamentação.</td>
                <td>Voos solicitados ao DECEA para permissão de uso do espaço aéreo.</td>
            </tr>
        </table>
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: left;">
                    Todas as operações possuem seguro aeronáutico <strong>R.E.T.A</strong> (Responsabilidade do Explorador ou Transportador Aéreo).
                </td>
                <td width="30%" style="border: none; text-align: center;">
                    <img src="{{ public_path('images/seguro.png') }}" style="max-height: 100px;">
                </td>
            </tr>
        </table>

        <h1>7. Termos e Condições</h1>
        <ul>
            @if($proposal->service_type == 'drone' || $proposal->service_type == 'tour_virtual')
                <li>Sujeito às condições climáticas de chuva e vento no local.</li>
                <li>Sujeito às condições de decolagem, voo e pouso no local.</li>
                <li>Não fazemos sobrevoos sobre pessoas ou condições de risco para terceiros.</li>
                <li>Não voamos em áreas que possuam pipas ou balões no local.</li>
                <li>Em caso de impossibilidade de voo devido a rajadas de vento maiores que 25mph, chuva, condições de risco ou falta de sinal GPS no local de voo impossibilitando a filmagem, poderá reagendar para uma nova data.</li>
            @endif

            @if($proposal->service_type == 'timelapse')
                <li>Acesso mensal ao equipamento para coleta de imagens.</li>
                <li>Câmeras não se conectam a internet.</li>
                <li>No caso de defeito no equipamento ou perda de imagem, o período não registrado não será cobrado até que o equipamento volte a funcionar normalmente.</li>
                <li>20 fotos por dia, tiradas a cada meia hora.</li>
                <li>A instalação do equipamento demanda local seguro.</li>
                <li>Se for necessária a reposição do local do equipamento, pedimos para avisar com antecedência de 2 semanas.</li>
                <li>A instalação e Remoção das câmeras serão responsabilidade da Tecnolabs.</li>
            @endif
        </ul>
        <p><strong>Essa proposta tem validade de 5 dias. Após o vencimento será necessário solicitar um novo orçamento e verificar a disponibilidade de data do serviço</strong>.</p>

        <h1>8. Observações</h1>
        <p>Essa proposta é única e exclusiva da <strong>{{ $proposal->client->name }}</strong> e não poderá ser repassada ou utilizada por outra empresa ou pessoa sem autorização prévia.</p>

        <h1>9. Considerações Finais</h1>
        <p>Se ao final da leitura, houver qualquer dúvida ou mesmo falta de alguma informação, solicitamos entrar em contato direto com a pessoa indicada abaixo, que foi a responsável pela elaboração desta proposta.</p>
        <p>Desde já a Tecnolabs agradece a atenção e estamos à disposição para mais informações.</p>

        <div class="signature">
            <p>Atenciosamente,</p>
            <br>
            <strong>Tecnolabs - Agência Digital</strong> | CNPJ: 44.310.501/0001-40<br>
             <strong>{{ Auth::user()->name }}</strong> | {{ Auth::user()->email }} | (82) 3142-0399
        </div>

    </div>
</body>
</html>