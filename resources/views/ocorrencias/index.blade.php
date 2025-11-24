@extends('layouts.app')

@section('title', 'Ocorrências')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <!-- 🔹 Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>
        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Tipo</label>
                    <input type="text" id="filtroTipo" class="form-control form-control-sm" placeholder="Ex: SLA, Atraso...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Contrato</label>
                    <input type="text" id="filtroContrato" class="form-control form-control-sm" placeholder="Número ou ID do contrato">
                </div>
                <div class="col-md-4 d-flex justify-content-end align-items-end">
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

    <!-- 🔹 Lista -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Ocorrências</h4>
            <a href="{{ route('ocorrencias.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nova Ocorrência
            </a>
        </div>
        <div class="card-body">
            <!-- 🔹 Navbar de ações -->
            <nav class="nav nav-pills mb-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a id="navDetalhes" class="nav-link disabled" href="#">
                            <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="navEditar" class="nav-link disabled" href="#">
                            <i class="fas fa-edit text-warning me-2"></i> Editar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="navExcluir" class="nav-link disabled" href="#">
                            <i class="fas fa-trash text-danger me-2"></i> Excluir
                        </a>
                    </li>
                </ul>
            </nav>

            <table id="tabelaOcorrencias" class="table table-striped no-inner-borders w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:45px;"></th>
                        <th>Tipo</th>
                        <th>Contrato</th>
                        <th>Data</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($ocorrencias)
                        @foreach($ocorrencias as $oc)
                            <tr>
                                <td class="text-center">
                                    <input type="radio" name="ocSelecionada" value="{{ $oc->id }}">
                                </td>
                                <td>{{ $oc->tipo }}</td>
                                <td>{{ $oc->contrato->numero ?? $oc->contrato_id ?? '-' }}</td>
                                <td>{{ $oc->data_ocorrencia ?? '-' }}</td>
                                <td class="text-muted small">{{ Str::limit($oc->descricao ?? '-', 60) }}</td>
                            </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.nav-link.disabled { opacity: 0.5; pointer-events: none; }
</style>
@endsection

@section('js')
<script>
$(function() {
    const tabela = $('#tabelaOcorrencias').DataTable({
        processing: false,
        serverSide: false,
        language: { url: '{{ asset("js/pt-BR.json") }}' },
        dom: 't<"bottom"p>',
        pageLength: 10,
        order: [[1, 'asc']]
    });

    let selecionado = null;
    $('#tabelaOcorrencias').on('change', 'input[name="ocSelecionada"]', function () {
        selecionado = $(this).val();
        $('#navDetalhes, #navEditar, #navExcluir').removeClass('disabled');
    });

    $('#navDetalhes').on('click', function (e) {
        e.preventDefault();
        if (!selecionado) return;
        window.location.href = '{{ url('ocorrencias') }}' + '/' + selecionado;
    });
    $('#navEditar').on('click', function (e) {
        e.preventDefault();
        if (!selecionado) return;
        window.location.href = '{{ url('ocorrencias') }}' + '/' + selecionado + '/edit';
    });
    $('#navExcluir').on('click', function (e) {
        e.preventDefault();
        if (!selecionado) return;
        if (!confirm('Deseja realmente excluir esta ocorrência?')) return;
        fetch('{{ url('ocorrencias') }}' + '/' + selecionado, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(() => location.reload());
    });

    $('#btnAplicarFiltros').on('click', function () {
        tabela.column(1).search($('#filtroTipo').val());
        tabela.column(2).search($('#filtroContrato').val());
        tabela.draw();
    });
    $('#btnLimpar').on('click', function () {
        $('#formFiltros')[0].reset();
        tabela.search('').columns().search('');
        tabela.order([1, 'asc']);
        $('#navDetalhes, #navEditar, #navExcluir').addClass('disabled');
        selecionado = null;
    });
});
</script>
@endsection
