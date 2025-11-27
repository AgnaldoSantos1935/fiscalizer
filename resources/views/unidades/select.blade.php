@extends('layouts.app')

@section('title','Selecionar Unidade')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white">Diretorias Regionais de Educação (DRE)</div>
        <div class="card-body">
            <p class="text-muted">Selecione a DRE para gerenciar o inventário regional. As escolas pertencem à abrangência da DRE.</p>
            <div class="list-group">
                @foreach($dres as $dre)
                    <form method="POST" action="{{ route('inventario.dres.acessar', $dre->id) }}" class="list-group-item d-flex justify-content-between align-items-center">
                        @csrf
                        <span>{{ $dre->nome_dre }}</span>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary">{{ $dre->municipio_sede }}</span>
                            <button class="btn btn-sm btn-primary" type="submit">Acessar Inventário</button>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
