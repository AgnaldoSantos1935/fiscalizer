@extends('layouts.app')
@section('title', 'Nova Escola')

@section('content')
@include('layouts.components.breadcrumbs')
@section('breadcrumb')
  @include('layouts.components.breadcrumbs', [
    'trail' => [
      ['label' => 'Escolas', 'icon' => 'fas fa-school', 'url' => route('escolas.index')],
      ['label' => 'Nova Escola']
    ]
  ])
@endsection
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Nova Escola
            </h4>
        </div>

        <form id="formNovaEscola" method="POST" action="{{ route('escolas.store') }}" class="p-4">
            @csrf

            <div id="alertArea" class="mb-2"></div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Código</label>
                    <input type="text" name="codigo" class="form-control form-control-sm" required placeholder="Ex: 01023">
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold small text-secondary">Nome da Escola</label>
                    <input type="text" name="nome" class="form-control form-control-sm" required placeholder="Digite o nome completo">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Município</label>
                    <input type="text" name="municipio" class="form-control form-control-sm" placeholder="Ex: Belém">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">UF</label>
                    <input type="text" name="uf" maxlength="2" class="form-control form-control-sm" placeholder="PA">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Cód. INEP</label>
                    <input type="text" name="codigo_inep" class="form-control form-control-sm" placeholder="12345678">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Telefone</label>
                    <input type="text" name="telefone" class="form-control form-control-sm" placeholder="(91) 99999-9999">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">CEP</label>
                    <input type="text" name="cep" class="form-control form-control-sm cep-input" placeholder="00000-000">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Logradouro</label>
                    <input type="text" name="logradouro" class="form-control form-control-sm" placeholder="Rua...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">Número</label>
                    <input type="text" name="numero" class="form-control form-control-sm" placeholder="Nº">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Complemento</label>
                    <input type="text" name="complemento" class="form-control form-control-sm" placeholder="Apto, bloco...">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Bairro</label>
                    <input type="text" name="bairro" class="form-control form-control-sm" placeholder="Bairro">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">DRE</label>
                    <select name="dre" class="form-select form-select-sm">
                        <option value="">Selecione...</option>
                        @foreach($dres as $dre)
                            <option value="{{ $dre->id }}">{{ $dre->nome_dre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4 gap-2">
                <a href="{{ route('escolas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-save me-1"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('css')
<!-- 🔹 SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.min.css">
<style>
.form-control-sm, .form-select-sm {
    border-radius: 10px;
}
.btn {
    border-radius: 20px;
}
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.20/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formNovaEscola');
    const $cep = document.querySelector('[name="cep"]');
    const $logradouro = document.querySelector('[name="logradouro"]');
    const $bairro = document.querySelector('[name="bairro"]');
    const $municipio = document.querySelector('[name="municipio"]');
    const $uf = document.querySelector('[name="uf"]');
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
      document.getElementById('alertArea').innerHTML = html;
    }

    function maskCEP(v){
      const d = (v||'').replace(/\D/g,'').slice(0,8);
      return d.length>5 ? d.slice(0,5)+'-'+d.slice(5) : d;
    }
    function limparEndereco(){
      if ($logradouro) $logradouro.value = '';
      if ($bairro) $bairro.value = '';
      // mantém município/UF se já preenchidos
    }
    function consultaCep(rawCep){
      const d = (rawCep||'').replace(/\D/g,'');
      if (d.length !== 8) return;
      setAlert('info','Consultando CEP','Buscando endereço no ViaCEP...');
      fetch(`https://viacep.com.br/ws/${d}/json/`)
        .then(r => r.json())
        .then(data => {
          if (data && data.erro) {
            limparEndereco();
            setAlert('warning','CEP não encontrado','Verifique o CEP informado.');
            if (typeof Swal !== 'undefined') {
              Swal.fire({ icon:'warning', title:'CEP não encontrado', text:'Verifique o CEP informado.' });
            }
            return;
          }
          if ($logradouro) $logradouro.value = data.logradouro || '';
          if ($bairro) $bairro.value = data.bairro || '';
          if ($municipio) $municipio.value = data.localidade || $municipio.value;
          if ($uf) $uf.value = (data.uf || $uf.value || '').toUpperCase();
          if ($complemento && data.complemento) $complemento.value = data.complemento;
          setTimeout(() => { if ($numero) $numero.focus(); }, 80);
          setAlert('success','CEP validado','Endereço preenchido automaticamente.');
        })
        .catch(() => {
          setAlert('error','Erro ao consultar CEP','Serviço ViaCEP indisponível no momento.');
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon:'error', title:'Erro ao consultar CEP', text:'Serviço ViaCEP indisponível no momento.' });
          }
        });
    }

    if ($cep) {
      $cep.addEventListener('input', function(){ this.value = maskCEP(this.value); });
      $cep.addEventListener('keyup', function(e){
        const d = (this.value||'').replace(/\D/g,'');
        clearTimeout(this.__cepTimer);
        if (d.length === 8) {
          this.__cepTimer = setTimeout(() => consultaCep(d), 250);
        }
      });
      $cep.addEventListener('blur', function(){
        const d = (this.value||'').replace(/\D/g,'');
        if (d.length === 8) { consultaCep(d); }
        else { setAlert('warning','CEP inválido','Informe um CEP com 8 dígitos.'); }
      });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro ao cadastrar.');
            return response.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Escola cadastrada!',
                text: 'O registro foi salvo com sucesso no sistema.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0d6efd'
            }).then(() => {
                window.location.href = "{{ route('escolas.index') }}";
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao salvar',
                text: 'Não foi possível cadastrar a escola. Verifique os campos e tente novamente.',
                confirmButtonText: 'Fechar',
                confirmButtonColor: '#dc3545'
            });
            setAlert('error','Erro ao salvar','Não foi possível cadastrar a escola. Verifique os campos e tente novamente.');
            console.error(error);
        });
    });
});
</script>
@endsection
