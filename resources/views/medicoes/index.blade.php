@extends('layouts.app')

@section('title', 'Medições')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">
        <i class="fas fa-clipboard-check text-primary"></i>
        Medições
    </h3>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Contrato</th>
                <th>Competência</th>
                <th>Tipo</th>
                <th>Valor Líquido</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicoes as $m)
            <tr>
                <td>{{ $m->id }}</td>
                <td>{{ $m->contrato->numero ?? '—' }}</td>
                <td>{{ $m->competencia }}</td>
                <td>{{ strtoupper($m->tipo) }}</td>
                <td>R$ {{ number_format($m->valor_liquido ?? 0, 2, ',', '.') }}</td>
                <td>{{ $m->status }}</td>
                <td>
                    <a href="{{ route('medicoes.show', $m->id) }}" class="btn btn-sm btn-outline-primary">
                        Ver
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $medicoes->links() }}
</div>
@endsection
