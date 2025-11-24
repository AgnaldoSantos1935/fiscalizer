@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Ordens de Fornecimento</h4>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Contrato</th>
                        <th>Contratada</th>
                        <th class="text-end">Valor Total</th>
                        <th>Emitida em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ofs as $of)
                    <tr>
                        <td>{{ $of->numero_of }}</td>
                        <td>{{ $of->contrato->numero ?? '—' }}</td>
                        <td>{{ optional($of->contrato->contratada)->razao_social ?? '—' }}</td>
                        <td class="text-end">R$ {{ number_format($of->valor_total, 2, ',', '.') }}</td>
                        <td>{{ optional($of->data_emissao)->format('d/m/Y H:i') }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('ordens_fornecimento.show', $of->id) }}">Ver</a>
                            @if($of->arquivo_pdf)
<a class="btn btn-sm btn-outline-secondary" href="{{ route('ordens_fornecimento.pdf', $of->id) }}" target="_blank" rel="noopener"><i class="fas fa-file-pdf"></i> Download PDF</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Nenhuma OF encontrada.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $ofs->links() }}
        </div>
    </div>
</div>
@endsection