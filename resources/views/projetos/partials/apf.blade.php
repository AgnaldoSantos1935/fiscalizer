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
        <table class="table table-striped w-100">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Total PF</th>
                    <th>Observação</th>
                    <th class="text-center" width="140">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($apfs ?? []) as $apf)
                    <tr>
                        <td>{{ $apf->id }}</td>
                        <td>{{ $apf->total_pf ?? 0 }}</td>
                        <td>{{ $apf->observacao ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ url('projetos/'.$projeto->id.'/apf/'.$apf->id.'/edit') }}" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ url('projetos/'.$projeto->id.'/apf/'.$apf->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir APF?');">
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
