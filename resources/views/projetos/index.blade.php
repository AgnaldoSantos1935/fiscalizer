@extends('layouts.app')
@php
    use Illuminate\Support\Str;
    $badgeMap = [
        'analise'              => 'secondary',
        'planejado'            => 'info',
        'em_execucao'          => 'primary',
        'homologacao'          => 'warning',
        'aguardando_pagamento' => 'dark',
        'concluido'            => 'success',
        'suspenso'             => 'danger',
        'cancelado'            => 'danger',
    ];
@endphp

@section('title', 'Projetos')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <!-- 🔹 Card de Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>

        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">
                <div class="col-md-4">
                    <label for="filtroBusca" class="form-label fw-semibold text-secondary small">Buscar</label>
                    <input type="text" id="filtroBusca" class="form-control form-control-sm" placeholder="Código, título, sistema...">
                </div>

                <div class="col-md-3">
                    <label for="filtroSituacao" class="form-label fw-semibold text-secondary small">Situação</label>
                    <select id="filtroSituacao" class="custom-select form-control-border">
                        <option value="">Todas</option>
                        @foreach($situacoes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex justify-content-end align-items-end">
                    <div class="d-flex w-100">
                        <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                        <button type="button" id="btnLimpar" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-undo me-1"></i> Limpar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Card Principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-code-branch me-2 text-primary"></i>Projetos Cadastrados
            </h4>
        </div>

        <div class="card-body bg-white">
            <!-- 🔹 Navbar de ações -->
            <nav class="nav nav-pills flex-column flex-sm-row">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a id="navDetalhes" class="nav-link disabled" href="#">
                            <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
                        </a>
                    </li>
                    <li class="nav-item">
                        @can('projetos.criar')
                        <a href="{{ route('projetos.create') }}" class="nav-link active">
                            <i class="fas fa-plus-circle me-1"></i> Novo Projeto
                        </a>
                        @endcan
                    </li>
                </ul>
            </nav>

            <br>

            <!-- 🔹 Legendas de situação -->
            <div id="legendaSituacoes" class="mb-3 d-flex flex-wrap align-items-center gap-2 small text-secondary">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                <span>Legenda de Situações:</span>
                <div id="listaLegendas" class="d-flex flex-wrap gap-2 ms-2">
                    @foreach($situacoes as $key => $label)
                        @php $cls = $badgeMap[$key] ?? 'secondary'; @endphp
                        <span class="badge bg-{{ $cls }}">{{ $label }}</span>
                    @endforeach
                </div>
            </div>

            <!-- 🔹 Tabela -->
            <table id="tabelaProjetos" class="table table-striped no-inner-borders w-100"></table>
        </div>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('js')
<script>
$(function() {
    const SITUACOES = @json($situacoes);
    const badgeMap = @json($badgeMap);

    function badgeClass(situacao) {
        return badgeMap[situacao] ? `badge bg-${badgeMap[situacao]}` : 'badge bg-secondary';
    }

    // Evita popups padrão do DataTables em erros AJAX
    $.fn.dataTable.ext.errMode = 'none';

    const tabela = $('#tabelaProjetos').DataTable({
        ajax: {
            url: `{{ route('api.projetos') }}`,
            type: 'GET',
            dataSrc: 'data',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Accept', 'application/json');
            },
            error: function (xhr, status, err) {
                console.error('Erro ao carregar projetos:', xhr.status, status, err, (xhr.responseText || '').slice(0, 200));
            }
        },
        language: { url: "{{ asset('js/pt-BR.json') }}" },
        pageLength: 10,
        order: [[1, 'asc']],
        dom: 't<"bottom"p>',
        responsive: true,
        columns: [
            {
                data: null,
                className: 'text-center',
                render: (d) => `<input type="radio" name="projetoSelecionado" value="${d.id}">`
            },
            { data: 'codigo', defaultContent: '—', title: 'Código' },
            { data: 'titulo', defaultContent: '—', title: 'Título' },
            {
                data: null,
                title: 'Sistema/Módulo',
                render: d => {
                    const sis = d.sistema || '';
                    const mod = d.modulo || '';
                    return sis && mod ? `${sis} / ${mod}` : (sis || mod || '—');
                }
            },
            {
                data: 'pf_planejado',
                title: 'PF Planejado',
                className: 'text-end fw-semibold',
                render: v => (v != null) ? Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) : '0,00'
            },
            {
                data: 'situacao',
                title: 'Situação',
                render: s => `<span class="${badgeClass(s)}">${SITUACOES[s] ?? s ?? '—'}</span>`
            }
        ]
    });

    let projetoSelecionado = null;
    $('#tabelaProjetos').on('change', 'input[name="projetoSelecionado"]', function () {
        projetoSelecionado = $(this).val();
        $('#navDetalhes').removeClass('disabled');
    });

    $('#navDetalhes').on('click', function (e) {
        e.preventDefault();
        if (!projetoSelecionado) return;
        window.location.href = '{{ url('projetos') }}' + '/' + projetoSelecionado;
    });

    $('#btnAplicarFiltros').on('click', function (e) {
        e.preventDefault();
        const busca = $('#filtroBusca').val().trim();
        const situacao = $('#filtroSituacao').val().trim().toLowerCase();

        tabela.column(1).search(busca);
        tabela.column(2).search(busca);
        tabela.column(3).search(busca);
        tabela.draw();

        // Filtro de situação (badge HTML)
        $('#tabelaProjetos tbody tr').each(function () {
            const badgeText = $(this).find('td:nth-child(6) span').text().trim().toLowerCase();
            const match = !situacao || badgeText.includes(situacao);
            $(this).toggle(match);
        });
    });

    $('#btnLimpar').on('click', function (e) {
        e.preventDefault();
        $('#formFiltros')[0].reset();
        tabela.search('');
        tabela.columns().search('');
        tabela.order([1, 'asc']);
        tabela.ajax.reload(null, false);
    });
});
</script>
@endsection
