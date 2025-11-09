@extends('layouts.app')

@section('title', 'Conexões (Links de Rede)')

@section('content_header')
<h1>
    <i class="fas fa-network-wired me-2 text-primary"></i>
    Conexões Cadastradas
</h1>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-secondary fw-semibold">
            <i class="fas fa-filter me-2 text-primary"></i>Filtros de Pesquisa
        </h5>
        <a href="{{ route('hosts.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus me-1"></i> Nova Conexão
        </a>
    </div>

    <div class="card-body">
        <form id="formFiltros" class="row g-3 align-items-end">
            <!-- Contrato -->
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Contrato</label>
                <select id="filtroContrato" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($contratos as $contrato)
                        <option value="{{ $contrato->id }}">{{ $contrato->numero }} – {{ Str::limit($contrato->objeto, 50) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Item -->
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Item Contratual</label>
                <select id="filtroItem" class="form-select form-select-sm">
                    <option value="">Todos</option>
                </select>
            </div>

            <!-- Provedor -->
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Provedor</label>
                <input type="text" id="filtroProvedor" class="form-control form-control-sm" placeholder="Ex: Starlink, Vivo...">
            </div>

            <!-- Status -->
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-secondary">Status</label>
                <select id="filtroStatus" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                    <option value="em manutenção">Em manutenção</option>
                </select>
            </div>

            <div class="col-md-1 text-end">
                <button type="button" id="btnFiltrar" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <table id="tabelaHosts" class="table table-bordered table-striped table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Escola / Setor</th>
                    <th>Provedor</th>
                    <th>Tecnologia</th>
                    <th>IP</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection
@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function() {
    const tabela = $('#tabelaHosts').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route('hosts.index') }}',
            data: function (d) {
                d.contrato_id = $('#filtroContrato').val();
                d.item_id = $('#filtroItem').val();
                d.provedor = $('#filtroProvedor').val();
                d.status = $('#filtroStatus').val();
            }
        },
        columns: [
            { data: 'id', name: 'id', visible: false },
            { data: 'contrato', name: 'contrato', width: '15%',  visible: false },
            { data: 'item', name: 'item', width: '20%',  visible: false },
            { data: 'escola', name: 'escola', width: '20%' },
            { data: 'provedor', name: 'provedor', width: '10%' },
            { data: 'tecnologia', name: 'tecnologia', width: '10%' },
            { data: 'ip_atingivel', name: 'ip_atingivel', width: '10%' },
            {
                data: 'status', name: 'status', width: '8%',
                render: function(data) {
                    let badge = 'secondary';
                    if (data === 'ativo') badge = 'success';
                    else if (data === 'inativo') badge = 'danger';
                    else if (data === 'em manutenção') badge = 'warning';
                    return `<span class="badge bg-${badge} text-uppercase">${data}</span>`;
                }
            },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false, width: '8%' },
        ],
        language: { url: 'js/pt-BR.json' },
        order: [[0, 'desc']],
    });

    // 🔹 Filtro de contrato → carrega itens via AJAX
    $('#filtroContrato').on('change', async function() {
        const contratoId = $(this).val();
        const itemSelect = $('#filtroItem');
        itemSelect.html('<option value="">Carregando...</option>');

        if (!contratoId) {
            itemSelect.html('<option value="">Todos</option>');
            return;
        }

        try {
            const resp = await fetch(`/api/contratos/${contratoId}/itens`);
            const itens = await resp.json();
            itemSelect.html('<option value="">Todos</option>');
            itens.forEach(i => itemSelect.append(`<option value="${i.id}">${i.descricao_item}</option>`));
        } catch (err) {
            console.error('Erro ao carregar itens:', err);
            itemSelect.html('<option value="">Erro</option>');
        }
    });

    $('#btnFiltrar').click(() => tabela.ajax.reload());
});
</script>
@stop
