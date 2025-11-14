<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-secondary fw-semibold">
            <i class="fas fa-tasks text-primary me-2"></i> Atividades Técnicas
        </h5>

        <a href="{{ route('atividade.create', $projeto->id) }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Nova Atividade
        </a>
    </div>

    <div class="card-body">
        <table id="tabelaAtividades" class="table table-striped w-100"></table>
    </div>
</div>

@section('js')
<script>
$(document).ready(function () {

    $('#tabelaAtividades').DataTable({
        ajax: "{{ route('api.projetos.atividades', $projeto->id) }}",
        language: { url: '{{ asset("js/pt-BR.json") }}' },
        pageLength: 10,
        dom: 't<"bottom"p>',
        columns: [
            { data: 'data', title: 'Data', render: d => d ?? '—' },
            { data: 'etapa', title: 'Etapa' },
            { data: 'analista', title: 'Analista' },
            { data: 'horas', title: 'Horas' },
            { data: 'descricao', title: 'Descrição' },
            {
                data: null,
                title: 'Ações',
                width: '120px',
                render: (d) => `
                    <a href="/atividades/${d.id}/edit" class="btn btn-warning btn-sm me-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm" onclick="excluirAtividade(${d.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `
            }
        ]
    });

});
</script>
@endsection
