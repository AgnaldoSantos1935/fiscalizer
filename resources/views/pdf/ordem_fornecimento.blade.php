@extends('pdf.layouts.base')

@section('title')
    Ordem de Fornecimento Nº {{ $of->numero_of }}
@endsection

@section('header_left')
    <strong>Ordem de Fornecimento</strong><br>
    Nº {{ $of->numero_of }}
@endsection

@section('header_right')
    Emitida em {{ optional($of->data_emissao)->format('d/m/Y H:i') }}
@endsection

@section('content')

    <div class="section">
        <h3>Órgão/Unidade</h3>
        <table>
            <tr><td><strong>Órgão/Entidade</strong></td><td>{{ $of->orgao_entidade ?? config('app.name') }}</td></tr>
            <tr><td><strong>Unidade Requisitante</strong></td><td>{{ $of->unidade_requisitante ?? '—' }}</td></tr>
            <tr><td><strong>CNPJ</strong></td><td>{{ $of->cnpj_orgao ?? '—' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Dados do Contrato</h3>
        <table>
            <tr><td><strong>Contrato Nº</strong></td><td>{{ $of->contrato_numero ?? ($contrato->numero ?? '—') }}</td></tr>
            <tr><td><strong>Processo de Contratação Nº</strong></td><td>{{ $of->processo_contratacao ?? ($empenho->processo ?? '—') }}</td></tr>
            <tr><td><strong>Modalidade</strong></td><td>{{ $of->modalidade ?? ($contrato->modalidade ?? '—') }}</td></tr>
            <tr><td><strong>Vigência</strong></td><td>{{ optional($of->vigencia_inicio ?? $contrato->data_inicio_vigencia)->format('d/m/Y') }} — {{ optional($of->vigencia_fim ?? $contrato->data_fim_vigencia)->format('d/m/Y') }}</td></tr>
            <tr><td><strong>Fundamentação Legal</strong></td><td>{{ $of->fundamentacao_legal ?? 'Lei nº 14.133/2021' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Dados da Contratada</h3>
        <table>
            <tr><td><strong>Razão Social</strong></td><td>{{ $of->contratada_razao_social ?? optional($contrato->contratada)->razao_social }}</td></tr>
            <tr><td><strong>CNPJ</strong></td><td>{{ $of->contratada_cnpj ?? optional($contrato->contratada)->cnpj }}</td></tr>
            <tr><td><strong>Endereço</strong></td><td>{{ $of->contratada_endereco ?? optional($contrato->contratada)->endereco }}</td></tr>
            <tr><td><strong>Representante Legal</strong></td><td>{{ $of->contratada_representante ?? '—' }}</td></tr>
            <tr><td><strong>Telefone/E-mail</strong></td><td>{{ $of->contratada_contato ?? '—' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Objeto da Ordem de Fornecimento</h3>
        <p>Atendendo à necessidade da Administração, determina-se o fornecimento dos itens abaixo, conforme especificações, quantidades e preços pactuados no contrato supracitado.</p>
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th class="right">Quantidade</th>
                    <th class="right">Valor Unit.</th>
                    <th class="right">Valor Total</th>
                </tr>
            </thead>
            <tbody>
            @foreach (($itens ?? []) as $item)
                <tr>
                    <td>{{ $item['descricao'] }}</td>
                    <td class="right">{{ number_format($item['quantidade'] ?? 0, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item['valor_unitario'] ?? 0, 2, ',', '.') }}</td>
                    <td class="right">R$ {{ number_format($item['valor_total'] ?? 0, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="right" style="margin-top: 8px;">
            <strong>Valor Total da Ordem:</strong>
            R$ {{ number_format(collect($itens ?? [])->sum(fn($i) => ($i['valor_total'] ?? 0)), 2, ',', '.') }}
        </div>
    </div>

    <div class="section">
        <h3>Prazo para Entrega</h3>
        <p>Nos termos do contrato, o prazo para entrega/execução é de <strong>{{ $of->prazo_entrega_dias ? ($of->prazo_entrega_dias.' dias') : '____ dias' }}</strong>, contado a partir do recebimento desta Ordem de Fornecimento, conforme art. 121 da Lei 14.133/2021.</p>
        <table>
            <tr><td><strong>Local de Entrega</strong></td><td>{{ $of->local_entrega ?? '—' }}</td></tr>
            <tr><td><strong>Horário</strong></td><td>{{ $of->horario_entrega ?? '—' }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Condições de Recebimento</h3>
        <p>
            O recebimento será realizado conforme arts. 140 e 141 da Lei 14.133/2021, observando-se:
            Recebimento provisório: conferência dos bens/serviços; Recebimento definitivo: após verificação da conformidade com o contrato; Recusa do item caso esteja em desacordo com as especificações contratuais.
            O recebimento será atestado pelo(s) fiscal(is) do contrato nomeado(s) pela autoridade competente.
        </p>
        @if(!empty($of->recebimento_condicoes))
        <p class="small">{{ $of->recebimento_condicoes }}</p>
        @endif
    </div>

    <div class="section">
        <h3>Obrigações</h3>
        <p><strong>Da Contratada:</strong> {{ $of->obrigacoes_contratada ?? 'Cumprir integralmente as condições do contrato e desta ordem; Garantir qualidade e conformidade técnica; Substituir itens em desacordo; Observar prazos e normas aplicáveis (art. 81, Lei 14.133).' }}</p>
        <p><strong>Da Administração:</strong> {{ $of->obrigacoes_administracao ?? 'Disponibilizar condições para entrega e conferência; Efetuar pagamento conforme regras contratuais; Registrar e comunicar irregularidades.' }}</p>
    </div>

    <div class="section">
        <h3>Sanções</h3>
        <p>{{ $of->sancoes ?? 'O descumprimento injustificado sujeitará a penalidades previstas no Contrato e nos arts. 156 a 168 da Lei nº 14.133/2021, bem como nas demais normas aplicáveis.' }}</p>
    </div>

    <div class="section">
        <h3>Autorização</h3>
        <p>Autoriza-se a emissão da presente Ordem de Fornecimento, para que produza seus efeitos legais.</p>
        <table>
            <tr><td><strong>Local/Data</strong></td><td>{{ optional($of->data_emissao)->format('d/m/Y') }}</td></tr>
        </table>
        <table style="margin-top: 8px;">
            <tr><td><strong>Autoridade Requisitante</strong></td><td>Nome: {{ $of->autoridade_nome ?? '—' }} • Cargo: {{ $of->autoridade_cargo ?? '—' }}</td></tr>
            <tr><td><strong>Gestor do Contrato</strong></td><td>Nome: {{ $of->gestor_nome ?? '—' }} • Portaria: {{ $of->gestor_portaria ?? '—' }}</td></tr>
            <tr><td><strong>Fiscal do Contrato</strong></td><td>Nome: {{ $of->fiscal_nome ?? '—' }} • Portaria: {{ $of->fiscal_portaria ?? '—' }}</td></tr>
            <tr><td><strong>Contratada</strong></td><td>Representante: {{ $of->contratada_representante ?? '—' }}</td></tr>
        </table>
        <p class="small" style="margin-top: 8px;">Documento gerado eletronicamente pelo Sistema Fiscalizer.</p>
    </div>
@endsection