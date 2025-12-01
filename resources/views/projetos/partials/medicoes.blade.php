<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-secondary fw-semibold">
            <i class="fas fa-clipboard-check text-primary me-2"></i> Itens de Medição
        </h5>

        <a href="{{ route('medicao.itens.create', $projeto->id) }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Cadastrar Item
        </a>
    </div>

    <div class="card-body bg-white">
        <table class="table table-striped w-100">
            <thead class="table-light">
                <tr>
                    <th>Descrição</th>
                    <th>PF</th>
                    <th>UST</h>
                    <th class="text-end">Valor Total</th>
                    <th class="text-center" width="130">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($medicaoItens ?? []) as $mi)
                    <tr>
                        <td>{{ $mi->descricao ?? '—' }}</td>
                        <td>{{ $mi->pontos_funcao ?? 0 }}</td>
                        <td>{{ $mi->ust ?? 0 }}</td>
                        <td class="text-end">R$ {{ number_format((float) ($mi->valor_total ?? 0), 2, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ url('medicao/itens') }}/{{ $mi->id }}/edit" class="btn btn-warning btn-sm me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ url('medicao/itens') }}/{{ $mi->id }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir item?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('js')
<script>$(function(){ /* sem DataTables */ });</script>
@endpush
