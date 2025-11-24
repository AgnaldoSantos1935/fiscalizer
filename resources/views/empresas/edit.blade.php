@extends('layouts.app')

@section('title', 'Editar Empresa')

@section('content')
@include('layouts.components.breadcrumbs')
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

                <div id="alertArea" class="mb-2"></div>

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
                    <input type="text" name="cep" id="cep" class="form-control form-control-sm rounded-3 cep-input" value="{{ $empresa->cep }}" placeholder="00000-000" maxlength="9">
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
  const $cep = document.getElementById('cep');
  const $logradouro = document.getElementById('logradouro');
  const $bairro = document.getElementById('bairro');
  const $cidade = document.querySelector('[name="cidade"]');
  const $uf = document.querySelector('[name="uf"]');
  const $numero = document.getElementById('numero');
  const $complemento = document.getElementById('complemento');

  function setAlert(type, title, message) {
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-triangle', warning: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const classes = { success: 'alert-success', error: 'alert-danger', warning: 'alert-warning', info: 'alert-info' };
    const html = `
      <div class="alert ${classes[type] || classes.info} alert-dismissible fade show" role="alert">
        <i class="fas ${icons[type] || icons.info} me-1"></i> <strong>${title}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>`;
    const area = document.getElementById('alertArea');
    if (area) area.innerHTML = html;
  }

  function maskCEP(v){ const d=(v||'').replace(/\D/g,'').slice(0,8); return d.length>5? d.slice(0,5)+'-'+d.slice(5): d; }
  function limparEndereco(){
    if ($logradouro) $logradouro.value = '';
    if ($bairro) $bairro.value = '';
    // mantém cidade/UF se já preenchidos
  }
  function consultaCep(rawCep){
    const d = (rawCep||'').replace(/\D/g,''); if(d.length!==8) return;
    setAlert('info','Consultando CEP','Buscando endereço no ViaCEP...');
    fetch(`https://viacep.com.br/ws/${d}/json/`)
      .then(r=>r.json())
      .then(data=>{
        if(data && data.erro){
          limparEndereco();
          setAlert('warning','CEP não encontrado','Verifique o CEP informado.');
          if (typeof Swal !== 'undefined') { Swal.fire({icon:'warning', title:'CEP não encontrado', text:'Verifique o CEP informado.'}); }
          return;
        }
        if ($logradouro) $logradouro.value = data.logradouro || '';
        if ($bairro) $bairro.value = data.bairro || '';
        if ($cidade) $cidade.value = data.localidade || ($cidade?.value||'');
        if ($uf) $uf.value = (data.uf || ($uf?.value||'')).toUpperCase();
        if ($complemento && data.complemento) $complemento.value = data.complemento;
        setTimeout(()=>{ if ($numero) $numero.focus(); }, 80);
        setAlert('success','CEP validado','Endereço preenchido automaticamente.');
      })
      .catch(()=>{
        setAlert('error','Erro ao consultar CEP','Serviço ViaCEP indisponível no momento.');
        if (typeof Swal !== 'undefined') { Swal.fire({icon:'error', title:'Erro ao consultar CEP', text:'Serviço ViaCEP indisponível no momento.'}); }
      });
  }

  document.addEventListener('DOMContentLoaded', function(){
    if($cep){
      $cep.addEventListener('input', function(){ this.value = maskCEP(this.value); });
      $cep.addEventListener('keyup', function(){
        const d = (this.value||'').replace(/\D/g,'');
        clearTimeout(this.__cepTimer);
        if (d.length === 8) {
          this.__cepTimer = setTimeout(()=>consultaCep(d), 250);
        }
      });
      $cep.addEventListener('blur', function(){
        const d = (this.value||'').replace(/\D/g,'');
        if (d.length === 8) { consultaCep(d); }
        else { setAlert('warning','CEP inválido','Informe um CEP com 8 dígitos.'); }
      });
    }
  });
})();
</script>
@endsection
