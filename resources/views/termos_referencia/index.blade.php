@extends('layouts.app')
@section('title', 'Termos de Referência')

@section('content_body')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold"><i class="fas fa-list-ul text-primary me-2"></i>Termos de Referência</h4>
            <a href="{{ route('contratacoes.termos-referencia.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Novo Termo
            </a>
        </div>
        <div class="card-body bg-white">
            <!-- 🔹 Navbar de ações -->
            <nav class="nav nav-pills flex-column flex-sm-row mb-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a id="navDetalhes" class="nav-link disabled" aria-current="page" href="#">
                            <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contratacoes.termos-referencia.create') }}" class="nav-link active" aria-current="page">
                            <i class="fas fa-plus-circle me-1"></i> Novo Termo
                        </a>
                    </li>
                </ul>
            </nav>
            <table class="table table-striped table-hover align-middle no-inner-borders w-100" id="trs-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px" class="text-center">Sel.</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Valor Estimado</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
    const tabela = $('#trs-table').DataTable({
        ajax: {
            url: "{{ route('contratacoes.termos-referencia.api') }}",
            dataSrc: 'data'
        },
        dom: 't<"bottom"p>',
        pageLength: 10,
        order: [[1, 'asc']],
        responsive: true,
        columns: [
            {
                data: null,
                className: 'text-center', orderable: false,
                render: function(row){
                    return `<input type="radio" name="trSelecionado" value="${row.show_url}">`;
                }
            },
            { data: 'titulo' },
            { data: 'status', render: function(val){ return `<span class="badge bg-secondary">${val ?? '—'}</span>`; } },
            { data: 'valor_estimado', render: function(val){
                if (val === null || val === undefined || val === '') return '—';
                var num = parseFloat(val);
                if (isNaN(num)) return val;
                return 'R$ ' + num.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            } },
            { data: null, orderable: false, render: function(row){
                return `
                    <div class="text-end">
                        <a href="${row.show_url}" class="btn btn-outline-primary btn-sm">Detalhes</a>
                        <a href="${row.edit_url}" class="btn btn-outline-warning btn-sm">Editar</a>
                        <a href="${row.pdf_url}" class="btn btn-outline-success btn-sm">Gerar PDF</a>
                    </div>`;
            }}
        ],
        language: {
            url: "{{ asset('js/pt-BR.json') }}"
        }
    });

    let trSelecionado = null;
    $('#trs-table').on('change', 'input[name="trSelecionado"]', function () {
        trSelecionado = $(this).val();
        $('#navDetalhes').removeClass('disabled');
    });

    $('#navDetalhes').on('click', function (e) {
        e.preventDefault();
        if (!trSelecionado) return;
        window.location.href = trSelecionado;
    });
});
</script>
@endpush
