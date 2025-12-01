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
        <table class="table table-striped w-100">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>PF</th>
                    <th>UST</th>
                    <th class="text-end">Valor</th>
                    <th>Emissão</th>
                    <th class="text-center" width="150">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($boletins ?? []) as $b)
                    <tr>
                        <td>{{ $b->id }}</td>
                        <td>{{ $b->total_pf ?? 0 }}</td>
                        <td>{{ $b->total_ust ?? 0 }}</td>
                        <td class="text-end">R$ {{ number_format((float) ($b->valor_total ?? 0), 2, ',', '.') }}</td>
                        <td>{{ optional($b->data_emissao)->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ url('boletins') }}/{{ $b->id }}" class="btn btn-info btn-sm me-1"><i class="fas fa-eye"></i></a>
                            <a href="{{ url('boletins') }}/{{ $b->id }}/pdf" class="btn btn-secondary btn-sm me-1"><i class="fas fa-file-pdf"></i></a>
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
