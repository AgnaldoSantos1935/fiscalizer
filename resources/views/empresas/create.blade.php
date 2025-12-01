@extends('layouts.app')
@section('title', 'Cadastrar Empresa')

@section('content')
@include('layouts.components.breadcrumbs')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => (request('return') === 'contratos.create')
      ? [
          ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
          ['label' => 'Cadastrar Contrato', 'url' => route('contratos.create')],
          ['label' => 'Cadastrar Empresa']
        ]
      : [
          ['label' => 'Empresas', 'icon' => 'fas fa-building', 'url' => route('empresas.index')],
          ['label' => 'Cadastrar Empresa']
        ]
  ])
@endsection
<div class="container-fluid">

    <!-- 🔹 Card principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body bg-white">

            <form action="{{ route('empresas.store') }}" method="POST" class="p-3">
                @csrf
                @if(request('return'))
                  <input type="hidden" name="return" value="{{ request('return') }}">
                @endif

                <div id="alertArea" class="mb-2"></div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="razao_social" class="form-label fw-semibold small text-secondary">
                            Razão Social <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="razao_social" id="razao_social"
                               class="form-control form-control-sm @error('razao_social') is-invalid @enderror"
                               value="{{ old('razao_social') }}" required>
                        @error('razao_social')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nome_fantasia" class="form-label fw-semibold small text-secondary">
                            Nome Fantasia
                        </label>
                        <input type="text" name="nome_fantasia" id="nome_fantasia"
                               class="form-control form-control-sm"
                               value="{{ old('nome_fantasia') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="cnpj" class="form-label fw-semibold small text-secondary">
                            CNPJ <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="cnpj" id="cnpj"
                               class="form-control form-control-sm cnpj-input @error('cnpj') is-invalid @enderror"
                               value="{{ old('cnpj', request('cnpj')) }}" required>
                        @error('cnpj')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="inscricao_estadual" class="form-label fw-semibold small text-secondary">
                            Inscrição Estadual
                        </label>
                        <input type="text" name="inscricao_estadual" id="inscricao_estadual"
                               class="form-control form-control-sm"
                               value="{{ old('inscricao_estadual') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label fw-semibold small text-secondary">E-mail</label>
                        <input type="email" name="email" id="email"
                               class="form-control form-control-sm"
                               value="{{ old('email') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="telefone" class="form-label fw-semibold small text-secondary">Telefone</label>
                        <input type="text" name="telefone" id="telefone"
                               class="form-control form-control-sm"
                               value="{{ old('telefone') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="cep" class="form-label fw-semibold small text-secondary">CEP</label>
                        <input type="text" name="cep" id="cep" class="form-control form-control-sm cep-input" value="{{ old('cep') }}" placeholder="00000-000" maxlength="9">
                    </div>
                    <div class="col-md-5">
                        <label for="logradouro" class="form-label fw-semibold small text-secondary">Logradouro</label>
                        <input type="text" name="logradouro" id="logradouro" class="form-control form-control-sm" value="{{ old('logradouro') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="numero" class="form-label fw-semibold small text-secondary">Número</label>
                        <input type="text" name="numero" id="numero" class="form-control form-control-sm" value="{{ old('numero') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="complemento" class="form-label fw-semibold small text-secondary">Complemento</label>
                        <input type="text" name="complemento" id="complemento" class="form-control form-control-sm" value="{{ old('complemento') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label fw-semibold small text-secondary">Bairro</label>
                        <input type="text" name="bairro" id="bairro" class="form-control form-control-sm" value="{{ old('bairro') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="cidade" class="form-label fw-semibold small text-secondary">Cidade</label>
                        <input type="text" name="cidade" id="cidade"
                               class="form-control form-control-sm"
                               value="{{ old('cidade') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="uf" class="form-label fw-semibold small text-secondary">UF</label>
                        <input type="text" name="uf" id="uf" maxlength="2"
                               class="form-control form-control-sm text-uppercase"
                               value="{{ old('uf') }}">
                    </div>
                </div>

                <!-- 🔹 Rodapé -->
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3"><i class="fas fa-undo me-1"></i> Limpar</button>
                    @if(request('return'))
                      <a href="{{ route(request('return')) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times me-1"></i> Cancelar</a>
                    @else
                      <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times me-1"></i> Cancelar</a>
                    @endif
                    <button type="submit" class="btn btn-success btn-sm px-3"><i class="fas fa-save me-1"></i> Salvar Empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
form label { font-size: 0.85rem; }
form input, form select {
    border-radius: 10px !important;
}
form .btn {
    border-radius: 20px !important;
}
.card {
    border: none !important;
}
</style>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>
<script>
(function(){
  const $cep = document.getElementById('cep');
  const $logradouro = document.getElementById('logradouro');
  const $bairro = document.getElementById('bairro');
  const $cidade = document.getElementById('cidade');
  const $uf = document.getElementById('uf');
  const $numero = document.getElementById('numero');
  const $complemento = document.getElementById('complemento');
  const $cnpj = document.getElementById('cnpj');
  const $razao = document.getElementById('razao_social');
  const $fantasia = document.getElementById('nome_fantasia');
  const $email = document.getElementById('email');
  const $telefone = document.getElementById('telefone');

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

  function maskCNPJ(v){
    const d=(v||'').replace(/\D/g,'').slice(0,14);
    let out='';
    for(let i=0;i<d.length;i++){
      out+=d[i];
      if(i===1) out+='.';
      if(i===4) out+='.';
      if(i===7) out+='/';
      if(i===11) out+='-';
    }
    return out;
  }

  function isValidCnpjDigits(c){
    const d=(c||'').replace(/\D/g,'');
    if(d.length!==14) return false;
    if(/^([0-9])\1{13}$/.test(d)) return false;
    const calc=(base,len)=>{
      let sum=0; let pos=len-7;
      for(let i=0;i<len;i++){ sum+=parseInt(base[i],10)*pos; pos--; if(pos<2) pos=9; }
      const res=sum%11; return res<2?0:(11-res);
    };
    const d1=calc(d,12); if(parseInt(d[12],10)!==d1) return false;
    const d2=calc(d,13); return parseInt(d[13],10)===d2;
  }

  function preencherCamposEmpresa(data){
    if(!data) return;
    if ($razao && data.razao_social) $razao.value = data.razao_social;
    if ($fantasia && data.nome_fantasia) $fantasia.value = data.nome_fantasia;
    if ($email && data.email) $email.value = data.email;
    if ($telefone && (data.telefone || data.ddd_telefone_1)) $telefone.value = (data.telefone || data.ddd_telefone_1);
    if ($cep && (data.cep)) { $cep.value = maskCEP(String(data.cep)); }
    if ($logradouro && data.logradouro) $logradouro.value = data.logradouro;
    if ($numero && data.numero) $numero.value = String(data.numero);
    if ($complemento && data.complemento) $complemento.value = data.complemento;
    if ($bairro && data.bairro) $bairro.value = data.bairro;
    if ($cidade && (data.municipio || data.cidade)) $cidade.value = (data.municipio || data.cidade);
    if ($uf && data.uf) $uf.value = String(data.uf).toUpperCase();
  }

  function consultarCnpj(raw){
    const d=(raw||'').replace(/\D/g,'');
    if(d.length!==14){ setAlert('warning','CNPJ inválido','Informe um CNPJ com 14 dígitos.'); return; }
    if(!isValidCnpjDigits(d)){ setAlert('error','CNPJ inválido','Os dígitos do CNPJ não conferem.'); return; }
    setAlert('info','Consultando CNPJ','Verificando cadastro e buscando dados...');
    fetch(`${window.location.origin}/fiscalizer/public/empresas/verificar?cnpj=${d}`)
      .then(r=>r.json())
      .then(j=>{
        if(j && j.found && j.data){
          preencherCamposEmpresa(j.data);
          setAlert('warning','Empresa já cadastrada','Os dados foram carregados do cadastro interno.');
          if (typeof Swal !== 'undefined') { Swal.fire({icon:'warning', title:'Empresa já cadastrada', text:'Os dados foram carregados do cadastro interno.'}); }
          return;
        }
        return fetch(`https://brasilapi.com.br/api/cnpj/v1/${d}`)
          .then(r=>r.json())
          .then(data=>{
            if(data && data.razao_social){
              preencherCamposEmpresa(data);
              setAlert('success','Dados carregados','Campos preenchidos com dados da BrasilAPI.');
            } else {
              setAlert('warning','CNPJ não encontrado','Não foi possível obter dados para o CNPJ informado.');
            }
          })
          .catch(()=>{
            setAlert('error','Erro ao consultar CNPJ','Serviço BrasilAPI indisponível no momento.');
            if (typeof Swal !== 'undefined') { Swal.fire({icon:'error', title:'Erro ao consultar CNPJ', text:'Serviço BrasilAPI indisponível no momento.'}); }
          });
      })
      .catch(()=>{
        setAlert('error','Erro ao verificar cadastro','Não foi possível verificar o cadastro interno.');
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

    if($cnpj){
      $cnpj.addEventListener('input', function(){ this.value = maskCNPJ(this.value); });
      $cnpj.addEventListener('keyup', function(){
        const d=(this.value||'').replace(/\D/g,'');
        clearTimeout(this.__cnpjTimer);
        if(d.length===14 && isValidCnpjDigits(d)){
          this.__cnpjTimer = setTimeout(()=>consultarCnpj(d), 250);
        }
      });
      $cnpj.addEventListener('blur', function(){
        const d=(this.value||'').replace(/\D/g,'');
        if(d.length===14){ consultarCnpj(d); }
        else { setAlert('warning','CNPJ inválido','Informe um CNPJ com 14 dígitos.'); }
      });
    }
  });
})();
</script>
@endsection
