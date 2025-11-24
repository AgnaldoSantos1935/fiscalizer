@extends('layouts.app')

@section('title', 'Cadastrar DRE')

@section('content')
@include('layouts.components.breadcrumbs')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'DREs', 'icon' => 'fas fa-university', 'url' => route('dres.index')],
      ['label' => 'Nova DRE']
    ]
  ])
@endsection
<div class="container-fluid">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between rounded-top-4">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nova DRE</h4>
            <a href="{{ route('dres.index') }}" class="btn btn-light btn-sm text-primary">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
        </div>

        <div class="card-body bg-light p-4">
            <form action="{{ route('dres.store') }}" method="POST">
                @csrf
                <div id="alertArea" class="mb-2"></div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Código *</label>
                        <input type="text" name="codigodre" class="form-control" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Nome *</label>
                        <input type="text" name="nome_dre" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Município Sede *</label>
                        <input type="text" name="municipio_sede" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Telefone</label>
                        <input type="text" name="telefone" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">CEP</label>
                        <input type="text" name="cep" class="form-control cep-input" placeholder="00000-000" maxlength="9">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Logradouro</label>
                        <input type="text" name="logradouro" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Número</label>
                        <input type="text" name="numero" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Complemento</label>
                        <input type="text" name="complemento" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Bairro</label>
                        <input type="text" name="bairro" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Latitude</label>
                        <input type="text" name="latitude" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Longitude</label>
                        <input type="text" name="longitude" class="form-control">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('dres.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
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
    // mantém município se preenchido
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
        if ($municipio) $municipio.value = data.localidade || $municipio.value;
        if ($complemento && data.complemento) $complemento.value = data.complemento;
        setTimeout(()=>{ if ($numero) $numero.focus(); }, 80);
        setAlert('success','CEP validado','Endereço preenchido automaticamente.');
      })
      .catch(()=>{
        setAlert('error','Erro ao consultar CEP','Serviço ViaCEP indisponível no momento.');
        if (typeof Swal !== 'undefined') { Swal.fire({icon:'error', title:'Erro ao consultar CEP', text:'Serviço ViaCEP indisponível no momento.'}); }
      });
  }

  if ($cep) {
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
})();
</script>
@endsection
