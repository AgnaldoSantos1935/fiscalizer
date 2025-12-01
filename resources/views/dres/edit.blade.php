@extends('layouts.app')
@section('title','Editar DRE')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-white">
      <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar DRE</h5>
    </div>
    <form action="{{ route('dres.update', $dre->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="card-body row g-3">
        <div class="col-md-3">
          <label class="form-label">Código</label>
          <input type="text" name="codigodre" class="form-control" value="{{ old('codigodre', $dre->codigodre) }}" required>
        </div>
        <div class="col-md-9">
          <label class="form-label">Nome</label>
          <input type="text" name="nome_dre" class="form-control" value="{{ old('nome_dre', $dre->nome_dre) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Município Sede</label>
          <input type="text" name="municipio_sede" class="form-control" value="{{ old('municipio_sede', $dre->municipio_sede) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $dre->email) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $dre->telefone) }}">
        </div>
        <div class="col-md-12">
          <label class="form-label">Endereço</label>
          <input type="text" name="endereco" class="form-control" value="{{ old('endereco', $dre->endereco) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">CEP</label>
          <input type="text" name="cep" class="form-control" value="{{ old('cep', $dre->cep) }}" maxlength="9">
        </div>
        <div class="col-md-2">
          <label class="form-label">UF</label>
          <input type="text" name="uf" class="form-control" value="{{ old('uf', $dre->uf) }}" maxlength="2">
        </div>
        <div class="col-md-5">
          <label class="form-label">Logradouro</label>
          <input type="text" name="logradouro" class="form-control" value="{{ old('logradouro', $dre->logradouro) }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Número</label>
          <input type="text" name="numero" class="form-control" value="{{ old('numero', $dre->numero) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Complemento</label>
          <input type="text" name="complemento" class="form-control" value="{{ old('complemento', $dre->complemento) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Bairro</label>
          <input type="text" name="bairro" class="form-control" value="{{ old('bairro', $dre->bairro) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Latitude</label>
          <input type="text" name="latitude" class="form-control" value="{{ old('latitude', $dre->latitude) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Longitude</label>
          <input type="text" name="longitude" class="form-control" value="{{ old('longitude', $dre->longitude) }}">
        </div>
      </div>
      <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('dres.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Salvar</button>
      </div>
    </form>
  </div>
</div>
@endsection
@section('js')
<script>
(function(){
  const $cep = document.querySelector('[name="cep"]');
  const $logradouro = document.querySelector('[name="logradouro"]');
  const $bairro = document.querySelector('[name="bairro"]');
  const $municipio = document.querySelector('[name="municipio_sede"]');
  const $numero = document.querySelector('[name="numero"]');
  const $complemento = document.querySelector('[name="complemento"]');
  const $uf = document.querySelector('[name="uf"]');

  function maskCEP(v){ const d=(v||'').replace(/\D/g,'').slice(0,8); return d.length>5? d.slice(0,5)+'-'+d.slice(5): d; }
  function limparEndereco(){
    if ($logradouro) $logradouro.value = '';
    if ($bairro) $bairro.value = '';
  }
  function consultaCep(rawCep){
    const d = (rawCep||'').replace(/\D/g,''); if(d.length!==8) return;
    fetch(`https://viacep.com.br/ws/${d}/json/`).then(r=>r.json()).then(data=>{
      if(data && data.erro){ limparEndereco(); return; }
      if ($logradouro) $logradouro.value = data.logradouro || '';
      if ($bairro) $bairro.value = data.bairro || '';
      if ($municipio) $municipio.value = data.localidade || $municipio.value;
      if ($uf) $uf.value = (data.uf || $uf.value || '').toUpperCase();
      if ($complemento && data.complemento) $complemento.value = data.complemento;
      setTimeout(()=>{ if ($numero) $numero.focus(); }, 80);
    }).catch(()=>{});
  }

  if ($cep) {
    $cep.addEventListener('input', function(){ this.value = maskCEP(this.value); });
    $cep.addEventListener('keyup', function(){
      const d = (this.value||'').replace(/\D/g,'');
      clearTimeout(this.__cepTimer);
      if (d.length === 8) { this.__cepTimer = setTimeout(()=>consultaCep(d), 250); }
    });
    $cep.addEventListener('blur', function(){
      const d = (this.value||'').replace(/\D/g,'');
      if (d.length === 8) { consultaCep(d); }
    });
  }

  if ($uf) {
    $uf.addEventListener('input', function(){ this.value = (this.value||'').toUpperCase().slice(0,2); });
  }
})();
</script>
@endsection
