<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-secondary fw-semibold">
            <i class="fas fa-file-invoice-dollar text-primary me-2"></i> Boletins de Medição
        </h5>

        <a href="{{ route('boletins.create', $projeto->id) }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Gerar Boletim
        </a>
    </div>

    <div class="card-body bg-white">
        <table id="tabelaBoletins" class="table table-striped w-100"></table>
    </div>
</div>

@section('js')
<script>
$(document).ready(function () {

    $('#tabelaBoletins').DataTable({
        ajax: "{{ route('api.projetos.boletins', $projeto->id) }}",
        language: { url: '{{ asset("js/pt-BR.json") }}' },
        pageLength: 10,
        dom: 't<"bottom"p>',
        columns: [
            { data: 'id', title: 'ID' },
            { data: 'total_pf', title: 'PF' },
            { data: 'total_ust', title: 'UST' },
            { data: 'valor_total', title: 'Valor', render: v => 'R$ ' + parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) },
            { data: 'data_emissao', title: 'Emissão' },
            {
                data: null,
                title: 'Ações',
                width: '150px',
                render: (d) => `
                    <a href="/boletins/${d.id}" class="btn btn-info btn-sm me-1">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/boletins/${d.id}/pdf" class="btn btn-secondary btn-sm me-1">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                `
            }
        ]
    });

});
</script>
@endsection
