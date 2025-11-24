@extends('layouts.app')
@section('title', 'Editar Escola')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-edit me-2 text-warning"></i>Editar Escola
            </h4>
        </div>

        <form id="formEditarEscola" method="POST" action="{{ route('escolas.update', $escola->id) }}" class="p-4">
            @csrf
            @method('PUT')

            <div id="alertArea" class="mb-2"></div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $escola->codigo) }}" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold small text-secondary">Nome da Escola</label>
                    <input type="text" name="nome" value="{{ old('nome', $escola->Escola) }}" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Município</label>
                    <input type="text" name="municipio" value="{{ old('municipio', $escola->Municipio) }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">UF</label>
                    <input type="text" name="uf" maxlength="2" value="{{ old('uf', $escola->UF) }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Cód. INEP</label>
                    <input type="text" name="codigo_inep" value="{{ old('codigo_inep', $escola->inep) }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $escola->Telefone) }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">CEP</label>
                    <input type="text" name="cep" value="{{ old('cep', $escola->Cep ?? '') }}" class="form-control form-control-sm cep-input" placeholder="00000-000" maxlength="9">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Logradouro</label>
                    <input type="text" name="logradouro" value="{{ old('logradouro') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-secondary">Número</label>
                    <input type="text" name="numero" value="{{ old('numero') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Complemento</label>
                    <input type="text" name="complemento" value="{{ old('complemento') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-secondary">Bairro</label>
                    <input type="text" name="bairro" value="{{ old('bairro') }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">DRE</label>
                    <select name="dre" class="form-select form-select-sm">
                        <option value="">Selecione...</option>
                        @foreach($dres as $dre)
                            <option value="{{ $dre->id }}" {{ old('dre', $escola->dre_id ?? '') == $dre->id ? 'selected' : '' }}>
                                {{ $dre->nome_dre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('escolas.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <button type="submit" class="btn btn-warning btn-sm text-white px-3">
                    <i class="fas fa-save me-1"></i> Atualizar
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
    const form = document.getElementById('formEditarEscola');
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
            if (!response.ok) throw new Error('Erro ao atualizar.');
            return response.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Atualizado com sucesso!',
                text: 'Os dados da escola foram salvos corretamente.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#198754'
            }).then(() => {
                window.location.href = "{{ route('escolas.index') }}";
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erro ao salvar',
                text: 'Não foi possível atualizar os dados. Verifique o log do sistema.',
                confirmButtonText: 'Fechar',
                confirmButtonColor: '#dc3545'
            });
            setAlert('error','Erro ao salvar','Não foi possível atualizar os dados. Verifique os campos e tente novamente.');
            console.error(error);
        });
    });
});
</script>
@endsection
