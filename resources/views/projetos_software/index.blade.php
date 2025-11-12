@extends('layouts.app')
@section('title','Projetos de Software')
@section('content')
<div class="container-fluid">
<div class="card shadow-sm border-0 rounded-4 mb-4">
<div class="card-header bg-white d-flex justify-content-between align-items-center">
<h4 class="mb-0 text-secondary"><i class="fas fa-code-branch me-2 text-primary"></i>Projetos de Software</h4>
<a href="{{ route('projetos.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Novo</a>
</div>
<div class="card-body">
<form method="get" class="row g-2 mb-3">
<div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por código, título, sistema..."></div>
<div class="col-md-2"><button class="btn btn-outline-secondary w-100"><i class="fas fa-search"></i> Filtrar</button></div>
</form>
<div class="table-responsive">
<table class="table table-hover align-middle">
<thead><tr><th>Código</th><th>Título</th><th>Sistema/Módulo</th><th>PF</th><th>Situação</th><th></th></tr></thead>
<tbody>
@forelse($projetos as $p)
<tr>
<td class="fw-semibold">{{ $p->codigo }}</td>
<td>{{ $p->titulo }}</td>
<td>{{ $p->sistema }} / {{ $p->modulo }}</td>
<td>{{ number_format($p->pontos_funcao,2,',','.') }}</td>
<td><span class="badge bg-@php echo match($p->situacao){
'Analise'=>'secondary','Em Execucao'=>'info','Homologado'=>'success','Pago'=>'primary','Suspenso'=>'danger', default=>'secondary'}; @endphp">{{ $p->situacao }}</span></td>
<td class="text-end">
<a class="btn btn-sm btn-outline-primary" href="{{ route('projetos.show',$p) }}"><i class="fas fa-eye"></i></a>
<a class="btn btn-sm btn-outline-secondary" href="{{ route('projetos.edit',$p) }}"><i class="fas fa-pen"></i></a>
</td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">Nenhum projeto encontrado.</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $projetos->withQueryString()->links() }}
</div>
</div>
</div>
@endsection
