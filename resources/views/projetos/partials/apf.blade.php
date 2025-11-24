<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-secondary fw-semibold">
            <i class="fas fa-calculator text-primary me-2"></i> Análises de Pontos de Função (APF)
        </h5>

        <a href="{{ route('projetos.apf.create', $projeto->id) }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Nova APF
        </a>
    </div>

    <div class="card-body bg-white">
        <table id="tabelaApf" class="table table-striped w-100"></table>
    </div>
</div>

@section('js')
<script>
$(document).ready(function () {

    $('#tabelaApf').DataTable({
        ajax: "{{ route('api.projetos.apf', $projeto->id) }}",
        language: { url: '{{ asset("js/pt-BR.json") }}' },
        pageLength: 10,
        dom: 't<"bottom"ip>',
        columns: [
            { data: 'id', title: 'ID' },
            { data: 'total_pf', title: 'Total PF' },
            { data: 'observacao', title: 'Observação' },
            {
                data: null,
                title: 'Ações',
                render: d => `
                    <a href="/projetos/{{ $projeto->id }}/apf/${d.id}/edit"
                       class="btn btn-warning btn-sm me-1">
                       <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm" onclick="excluirApf(${d.id})">
                       <i class="fas fa-trash"></i>
                    </button>
                `
            },
        ]
    });

});
</script>
@endsection
