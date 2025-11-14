<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-secondary fw-semibold">
            <i class="fas fa-clipboard-check text-primary me-2"></i> Itens de Medição
        </h5>

        <a href="{{ route('medicao.itens.create', $projeto->id) }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Novo Item
        </a>
    </div>

    <div class="card-body bg-white">
        <table id="tabelaMedicao" class="table table-striped w-100"></table>
    </div>
</div>

@section('js')
<script>
$(document).ready(function () {

    $('#tabelaMedicao').DataTable({
        ajax: "{{ route('api.projetos.medicao', $projeto->id) }}",
        language: { url: '{{ asset("js/pt-BR.json") }}' },
        pageLength: 10,
        dom: 't<"bottom"p>',
        columns: [
            { data: 'descricao', title: 'Descrição' },
            { data: 'quantidade', title: 'Qtd' },
            { data: 'valor_unitario', title: 'Valor Unit.', render: v => 'R$ ' + parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) },
            { data: 'total', title: 'Total', render: v => 'R$ ' + parseFloat(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) },
            {
                data: null,
                title: 'Ações',
                width: '130px',
                render: (d) => `
                    <a href="/medicao/itens/${d.id}/edit" class="btn btn-warning btn-sm me-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm" onclick="excluirMedicao(${d.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `
            }
        ]
    });

});
</script>
@endsection
