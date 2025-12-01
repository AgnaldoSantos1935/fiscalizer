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
        <table class="table table-striped w-100">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th>Etapa</th>
                    <th>Analista</th>
                    <th class="text-end">Horas</th>
                    <th>Descrição</th>
                    <th class="text-center" width="120">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($atividades ?? []) as $a)
                    <tr>
                        <td class="text-center">{{ optional($a->data)->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $a->etapa ?? '—' }}</td>
                        <td>{{ $a->analista ?? '—' }}</td>
                        <td class="text-end">{{ number_format((float) ($a->horas ?? 0), 2, ',', '.') }}</td>
                        <td>{{ $a->descricao ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ url('atividades') }}/{{ $a->id }}/edit" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ url('atividades') }}/{{ $a->id }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir atividade?');">
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
