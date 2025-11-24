@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Ordem de Fornecimento Nº {{ $of->numero_of }}</h4>
        <div>
            @if($of->arquivo_pdf)
<a class="btn btn-outline-secondary" href="{{ route('ordens_fornecimento.pdf', $of->id) }}" target="_blank" rel="noopener"><i class="fas fa-file-pdf"></i> Download PDF</a>
            @endif
            <a class="btn btn-outline-primary" href="{{ route('ordens_fornecimento.index') }}">Voltar</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Dados do Contrato</h6>
                <p class="mb-1"><strong>Contrato Nº:</strong> {{ $of->contrato_numero ?? ($of->contrato->numero ?? '—') }}</p>
                <p class="mb-1"><strong>Processo de Contratação:</strong> {{ $of->processo_contratacao ?? '—' }}</p>
                <p class="mb-1"><strong>Modalidade:</strong> {{ $of->modalidade ?? '—' }}</p>
                <p class="mb-1"><strong>Vigência:</strong> {{ optional($of->vigencia_inicio)->format('d/m/Y') }} — {{ optional($of->vigencia_fim)->format('d/m/Y') }}</p>
                <p class="mb-1"><strong>Fundamentação Legal:</strong> {{ $of->fundamentacao_legal ?? 'Lei nº 14.133/2021' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Órgão e Unidade</h6>
                <p class="mb-1"><strong>Órgão/Entidade:</strong> {{ $of->orgao_entidade ?? '—' }}</p>
                <p class="mb-1"><strong>Unidade Requisitante:</strong> {{ $of->unidade_requisitante ?? '—' }}</p>
                <p class="mb-1"><strong>CNPJ:</strong> {{ $of->cnpj_orgao ?? '—' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Contratada</h6>
                <p class="mb-1"><strong>Razão Social:</strong> {{ $of->contratada_razao_social ?? optional($of->contrato->contratada)->razao_social }}</p>
                <p class="mb-1"><strong>CNPJ:</strong> {{ $of->contratada_cnpj ?? optional($of->contrato->contratada)->cnpj }}</p>
                <p class="mb-1"><strong>Endereço:</strong> {{ $of->contratada_endereco ?? optional($of->contrato->contratada)->endereco }}</p>
                <p class="mb-1"><strong>Representante:</strong> {{ $of->contratada_representante ?? '—' }}</p>
                <p class="mb-1"><strong>Contato:</strong> {{ $of->contratada_contato ?? '—' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Entrega</h6>
                <p class="mb-1"><strong>Prazo:</strong> {{ $of->prazo_entrega_dias ? $of->prazo_entrega_dias.' dias' : '—' }}</p>
                <p class="mb-1"><strong>Local:</strong> {{ $of->local_entrega ?? '—' }}</p>
                <p class="mb-1"><strong>Horário:</strong> {{ $of->horario_entrega ?? '—' }}</p>
            </div></div>
        </div>
    </div>

    <div class="card mt-3"><div class="card-body">
        <h6 class="fw-semibold">Itens da Ordem</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Descrição</th><th class="text-end">Qtde</th><th class="text-end">Vlr Unit</th><th class="text-end">Vlr Total</th></tr></thead>
                <tbody>
                @foreach(($of->itens_json ?? []) as $i)
                    <tr>
                        <td>{{ $i['descricao'] }}</td>
                        <td class="text-end">{{ number_format($i['quantidade'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-end">R$ {{ number_format($i['valor_unitario'] ?? 0, 2, ',', '.') }}</td>
                        <td class="text-end">R$ {{ number_format($i['valor_total'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-end"><strong>Total:</strong> R$ {{ number_format($of->valor_total, 2, ',', '.') }}</div>
    </div></div>

    <div class="row mt-3 g-3">
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Condições de Recebimento</h6>
                <p class="mb-0">{{ $of->recebimento_condicoes ?? '—' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Sanções</h6>
                <p class="mb-0">{{ $of->sancoes ?? '—' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Obrigações da Contratada</h6>
                <p class="mb-0">{{ $of->obrigacoes_contratada ?? '—' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h6 class="fw-semibold">Obrigações da Administração</h6>
                <p class="mb-0">{{ $of->obrigacoes_administracao ?? '—' }}</p>
            </div></div>
        </div>
    </div>

    <div class="card mt-3"><div class="card-body">
        <h6 class="fw-semibold">Autorizações e Assinaturas</h6>
        <div class="row">
            <div class="col-md-6"><p class="mb-1"><strong>Autoridade Requisitante:</strong> {{ $of->autoridade_nome ?? '—' }} ({{ $of->autoridade_cargo ?? '—' }})</p></div>
            <div class="col-md-6"><p class="mb-1"><strong>Gestor do Contrato:</strong> {{ $of->gestor_nome ?? '—' }} (Portaria: {{ $of->gestor_portaria ?? '—' }})</p></div>
            <div class="col-md-6"><p class="mb-1"><strong>Fiscal do Contrato:</strong> {{ $of->fiscal_nome ?? '—' }} (Portaria: {{ $of->fiscal_portaria ?? '—' }})</p></div>
        </div>
    </div></div>
</div>
@endsection