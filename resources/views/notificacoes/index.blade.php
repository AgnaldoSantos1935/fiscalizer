@extends('layouts.app')

@section('subtitle', 'Notificações')
@section('content_header_title', 'Notificações')
@section('content_header_subtitle', 'Todas as notificações')

@section('content_body')
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title mb-0">
            <i class="fas fa-bell me-2"></i>
            Minhas notificações
            <span class="badge bg-danger ms-3">Não lidas: {{ $naoLidas ?? 0 }}</span>
        </h3>
        <form action="{{ route('notificacoes.todas') }}" method="POST" class="ms-auto">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-check-double me-1"></i> Marcar todas como lidas
            </button>
        </form>
        <form action="{{ route('notificacoes.teste') }}" method="POST" class="ms-2">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-paper-plane me-1"></i> Enviar notificação de teste
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabelaNotificacoes" class="table table-hover mb-0 w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px">Status</th>
                        <th>Título</th>
                        <th class="d-none d-md-table-cell">Mensagem</th>
                        <th>Link</th>
                        <th class="text-nowrap" style="width: 160px">Recebida</th>
                        <th class="text-nowrap" style="width: 140px">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            Carregando via AJAX
        </div>
        <div class="small text-muted">
            Página dinâmica
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function() {
    let tabela;
    if ($.fn.dataTable.isDataTable('#tabelaNotificacoes')) {
        tabela = $('#tabelaNotificacoes').DataTable();
    } else {
        tabela = $('#tabelaNotificacoes').DataTable({
        processing: true,
        serverSide: false,
        ajax: { url: '{{ route('notificacoes.data') }}', dataSrc: 'data' },
        language: { url: window.DataTablesLangUrl },
        dom: 't<"bottom"p>',
        pageLength: 10,
        order: [[4, 'desc']],
        columns: [
            { data: 'lida', className: 'text-center', render: function(lida){
                return lida
                    ? '<span class="badge bg-secondary">Lida</span>'
                    : '<span class="badge bg-warning text-dark">Nova</span>';
            }, orderable: false, searchable: false },
            { data: 'titulo' },
            { data: 'mensagem', className: 'd-none d-md-table-cell text-muted' },
            { data: 'link', render: function(link){
                if (!link) return '<span class="text-muted">—</span>';
                return '<a href="'+link+'" class="text-primary" target="_blank">Abrir <i class="fas fa-external-link-alt ms-1"></i></a>';
            }, orderable: false, searchable: false },
            { data: 'recebida', className: 'text-nowrap' },
            { data: null, render: function(row){
                if (row.lida) return '<span class="text-muted">—</span>';
                return '<button type="button" class="btn btn-sm btn-outline-success btn-marcar-lida" data-id="'+row.id+'">\
                    <i class="fas fa-check me-1"></i> Marcar lida\
                </button>';
            }, orderable: false, searchable: false }
        ]
    });
    }

    $('#tabelaNotificacoes').on('click', '.btn-marcar-lida', function(){
        const id = $(this).data('id');
        fetch('{{ url('/notificacoes') }}' + '/' + id + '/lida', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => tabela.ajax.reload(null, false));
    });
});
</script>
@endpush
