@extends('layouts.app')
@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Hosts Monitorados')

@section('content_body')
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

                <div class="col-md-3">
                    <label for="filtroNome" class="form-label fw-semibold text-secondary small">Nome</label>
                    <input type="text" id="filtroNome" class="form-control form-control-sm" placeholder="Ex: Link Principal">
                </div>

                <div class="col-md-3">
                    <label for="filtroProvedor" class="form-label fw-semibold text-secondary small">Provedor</label>
                    <input type="text" id="filtroProvedor" class="form-control form-control-sm" placeholder="Ex: Claro, Vivo">
                </div>

                <div class="col-md-3">
                    <label for="filtroTipo" class="form-label fw-semibold text-secondary small">Tipo Monitoramento</label>
                    <select id="filtroTipo" class="custom-select form-control-border">
                        <option value="">Todos</option>
                        <option value="ping">Ping</option>
                        <option value="porta">Porta TCP</option>
                        <option value="http">HTTP</option>
                        <option value="snmp">SNMP</option>
                        <option value="mikrotik">Mikrotik</option>
                        <option value="speedtest">SpeedTest</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex justify-content-end align-items-end">
                    <div class="d-flex w-100">
                        <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                        <button type="button" id="btnLimpar" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-undo me-1"></i>Limpar
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
                <i class="fas fa-server me-2 text-primary"></i>Hosts Monitorados
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

                </ul>
            </nav>

            <br>

            <!-- 🔹 Legendas -->
            <div id="legendaTipos" class="mb-3 d-flex flex-wrap align-items-center gap-2 small text-secondary">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                <span>Legenda de Tipos:</span>
                <div id="listaLegendas" class="d-flex flex-wrap gap-2 ms-2"></div>
            </div>

            <!-- 🔹 Tabela -->
            <table id="tabelaHosts" class="table table-striped no-inner-borders w-100">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Host Alvo</th>
                        <th>Porta</th>
                        <th>Provedor</th>
                        <th>Descrição</th>
                        <th>Tecnologia</th>
                        <th>Tipo Monitoramento</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
</div>
@endsection

@section('css')
@endsection

@push('js')


<script>
$(document).ready(function () {

    // =====================================
    // 🔹 Monta legenda dos tipos
    // =====================================
    const tipos = [
        { nome: 'Ping', slug: 'ping', cor: 'primary', icon: 'fa-wave-square' },
        { nome: 'Porta TCP', slug: 'porta', cor: 'info', icon: 'fa-plug' },
        { nome: 'HTTP', slug: 'http', cor: 'warning', icon: 'fa-globe' },
        { nome: 'SNMP', slug: 'snmp', cor: 'success', icon: 'fa-network-wired' },
        { nome: 'Mikrotik', slug: 'mikrotik', cor: 'danger', icon: 'fa-bolt' },
        { nome: 'Speedtest', slug: 'speedtest', cor: 'dark', icon: 'fa-tachometer-alt' }
    ];

    const legenda = $('#listaLegendas');
    legenda.empty();

    tipos.forEach(t => {
        legenda.append(`
            <span class="badge bg-${t.cor} px-3 py-2 shadow-sm d-flex align-items-center gap-1">
                <i class="fas ${t.icon}"></i> ${t.nome}
            </span>
        `);
    });

    // =====================================
    // 🔹 Inicializa DataTable (AJAX)
    // =====================================
       let tabela = $('#tabelaHosts').DataTable({
    ajax: {
        url: `{{ route('api.hosts') }}`,
        data: function(d){
            d.nome = $('#filtroNome').val();
            d.provedor = $('#filtroProvedor').val();
            d.tipo = $('#filtroTipo').val();
        }
    },
    language: { url: window.DataTablesLangUrl },

    pageLength: 10,
    order: [[1, 'asc']],
    dom: 't<"bottom"p>',
    responsive: true,

    columns: [
        {
            data: 'id',
            title: '',
            className: 'text-center',
            orderable: false,
            render: (id) => `
                <input type="radio" name="hostSelecionado" value="${id}">
            `
        },

        { data: 'nome_conexao', title: 'Nome' },
        { data: 'ip_atingivel', title: 'Host Alvo' },

        {
            data: 'porta',
            title: 'Porta',
            render: p => p ? p : '—'
        },

        { data: 'provedor', title: 'Provedor' },
        { data: 'descricao', title: 'Descrição' },
        { data: 'tecnologia', title: 'Tecnologia' },

        {
            data: 'tipo_monitoramento',
            title: 'Tipo',
            render: (tipo) => {
                if (!tipo) return '<span class="badge bg-secondary">—</span>';

                const map = tipos.find(t => t.slug === tipo);

                return map
                    ? `<span class="badge bg-${map.cor}">${map.nome ?? tipo.toUpperCase()}</span>`
                    : `<span class="badge bg-secondary">${tipo.toUpperCase()}</span>`;
            }
        },

        {
            data: 'status',
            title: 'Status',
            render: s => s
                ? `<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Ativo</span>`
                : `<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Inativo</span>`
        }
    ]
});

    // =====================================
    // Controle do radio + botão detalhes
    // =====================================
    let hostSelecionado = null;

    $('#tabelaHosts').on('change', 'input[name="hostSelecionado"]', function () {
        hostSelecionado = $(this).val();
        $('#navDetalhes').removeClass('disabled');
    });

    $('#navDetalhes').on('click', function (e) {
        e.preventDefault();
        if (!hostSelecionado) return;
        window.location.href = "/hosts/" + hostSelecionado;
    });

    // =====================================
    // 🔍 Aplicar filtros
    // =====================================
    $('#btnAplicarFiltros').on('click', function () {
        tabela.ajax.reload();
    });

    // =====================================
    // 🔄 Limpar filtros
    // =====================================
    $('#btnLimpar').on('click', function () {
        $('#formFiltros')[0].reset();
        tabela.order([1, 'asc']);
        tabela.ajax.reload(null, false);

        hostSelecionado = null;
        $('#navDetalhes').addClass('disabled');
    });
    setInterval(() => {
    fetch("/api/hosts/status")
        .then(resp => resp.json())
        .then(data => {
            data.forEach(row => {
                let statusCell = $(`#host-status-${row.id}`);

                if (row.status == 1) {
                    statusCell.html(`<span class="badge bg-success">Online</span>`);
                } else {
                    statusCell.html(`<span class="badge bg-danger">Offline</span>`);
                }
            });
        });
}, 5000); // atualiza a cada 5s


});
</script>
@endpush
