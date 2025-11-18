@extends('layouts.app')

@section('title', 'Editar Empresa')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-edit me-2 text-warning"></i> Editar Empresa
            </h4>
            <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="card-body bg-white p-4">
            <form method="POST" action="{{ route('empresas.update', $empresa->id) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Razão Social</label>
                    <input type="text" name="razao_social" class="form-control form-control-sm rounded-3" value="{{ $empresa->razao_social }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-secondary">Nome Fantasia</label>
                    <input type="text" name="nome_fantasia" class="form-control form-control-sm rounded-3" value="{{ $empresa->nome_fantasia }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">CNPJ</label>
                    <input type="text" name="cnpj" class="form-control form-control-sm rounded-3 cnpj-input" value="{{ $empresa->cnpj }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Inscrição Estadual</label>
                    <input type="text" name="inscricao_estadual" class="form-control form-control-sm rounded-3" value="{{ $empresa->inscricao_estadual }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">E-mail</label>
                    <input type="email" name="email" class="form-control form-control-sm rounded-3" value="{{ $empresa->email }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Telefone</label>
                    <input type="text" name="telefone" class="form-control form-control-sm rounded-3" value="{{ $empresa->telefone }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">CEP</label>
                    <input type="text" name="cep" id="cep" class="form-control form-control-sm rounded-3 cep-input" value="{{ $empresa->cep }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" class="form-control form-control-sm rounded-3" value="{{ $empresa->logradouro }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control form-control-sm rounded-3" value="{{ $empresa->numero }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">Complemento</label>
                    <input type="text" name="complemento" id="complemento" class="form-control form-control-sm rounded-3" value="{{ $empresa->complemento }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control form-control-sm rounded-3" value="{{ $empresa->bairro }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Cidade</label>
                    <input type="text" name="cidade" class="form-control form-control-sm rounded-3" value="{{ $empresa->cidade }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">UF</label>
                    <input type="text" name="uf" maxlength="2" class="form-control form-control-sm rounded-3" value="{{ $empresa->uf }}">
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-warning btn-sm px-4 rounded-pill text-white">
                        <i class="fas fa-save me-1"></i> Atualizar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
(function(){
  function maskCEP(v){ var d=(v||'').replace(/\D/g,'').slice(0,8); return d.length>5? d.slice(0,5)+'-'+d.slice(5): d; }
  async function viaCEP(cep){ var d=(cep||'').replace(/\D/g,''); if(d.length!==8) return null; const r=await fetch('https://viacep.com.br/ws/'+d+'/json/'); const j=await r.json(); return (j && !j.erro)? j : null; }
  document.addEventListener('DOMContentLoaded', function(){
    var cep = document.getElementById('cep'); if(cep){ cep.addEventListener('input', function(){ cep.value = maskCEP(cep.value); }); cep.addEventListener('blur', async function(){ const data = await viaCEP(cep.value); if(data){ document.getElementById('logradouro').value=data.logradouro||''; document.getElementById('bairro').value=data.bairro||''; document.getElementById('cidade').value=data.localidade||''; document.getElementById('uf').value=(data.uf||'').toUpperCase(); } }); }
  });
})();
</script>
@endsection
