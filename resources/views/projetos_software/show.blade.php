@extends('layouts.app')
<td>{{ $a->numero }}</td><td>{{ $a->tipo }}</td>
<td>{{ number_format($a->pontos_funcao,2,',','.') }}</td>
<td>{{ $a->status }}</td>
<td>{{ optional($a->data_abertura)->format('d/m/Y') }}</td>
<td>{{ optional($a->data_homologacao)->format('d/m/Y') }}</td>
<td class="text-end">
<form method="post" action="{{ route('projetos.apfs.destroy',[$projeto,$a]) }}">@csrf @method('DELETE')
<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>


<div class="card rounded-4 shadow-sm">
<div class="card-header bg-white d-flex justify-content-between align-items-center">
<h5 class="mb-0">Fiscalizações</h5>
<button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#novaFisc">Nova Fiscalização</button>
</div>
<div class="card-body">
<div id="novaFisc" class="collapse">
<form method="post" action="{{ route('projetos.fiscalizacoes.store',$projeto) }}" class="row g-2 mb-3" enctype="multipart/form-data">
@csrf
<div class="col-md-3">
<select name="tipo_fiscalizacao" class="form-select" required>
<option value="Tecnica">Técnica</option>
<option value="Administrativa">Administrativa</option>
</select>
</div>
<div class="col-md-3"><input name="data_verificacao" type="date" class="form-control" required></div>
<div class="col-md-3"><input name="fiscal_responsavel" class="form-control" placeholder="Fiscal responsável" required></div>
<div class="col-md-3"><select name="status" class="form-select"><option>Pendente</option><option>Conforme</option><option>Nao Conforme</option></select></div>
<div class="col-12"><textarea name="descricao_verificacao" class="form-control" placeholder="Descrição / achados"></textarea></div>
<div class="col-md-3"><select name="nivel_risco" class="form-select"><option>Baixo</option><option>Medio</option><option>Alto</option></select></div>
<div class="col-md-6"><input type="file" name="arquivo" class="form-control" /></div>
<div class="col-md-3"><button class="btn btn-success w-100">Registrar</button></div>
</form>
</div>


<div class="table-responsive">
<table class="table table-sm align-middle">
<thead><tr><th>Data</th><th>Tipo</th><th>Status</th><th>Risco</th><th>Fiscal</th><th></th></tr></thead>
<tbody>
@foreach($projeto->fiscalizacoes as $f)
<tr>
<td>{{ optional($f->data_verificacao)->format('d/m/Y') }}</td>
<td>{{ $f->tipo_fiscalizacao }}</td>
<td>{{ $f->status }}</td>
<td>{{ $f->nivel_risco }}</td>
<td>{{ $f->fiscal_responsavel }}</td>
<td class="text-end">
<form method="post" action="{{ route('projetos.fiscalizacoes.destroy', $f) }}">@csrf @method('DELETE')
<button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>


</div>
</div>
</div>
@endsection
