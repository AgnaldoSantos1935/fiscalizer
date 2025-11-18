@extends('layouts.app')
@section('title', 'Nova Empresa')

@section('content')
@section('breadcrumb')
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item"><a href="{{ route('empresas.index') }}" class="text-decoration-none text-primary fw-semibold"><i class="fas fa-building me-1"></i> Empresas</a></li>
      <li class="breadcrumb-item active text-secondary fw-semibold">Nova Empresa</li>
    </ol>
  </nav>
@endsection
<div class="container-fluid">

    <!-- 🔹 Card principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body bg-white">

            <form action="{{ route('empresas.store') }}" method="POST" class="p-3">
                @csrf

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
                               value="{{ old('cnpj') }}" required>
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
                        <input type="text" name="cep" id="cep" class="form-control form-control-sm cep-input" value="{{ old('cep') }}">
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
                    <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times me-1"></i> Cancelar</a>
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
<script>
(function(){
  function maskCEP(v){
    var d = (v||'').replace(/\D/g,'').slice(0,8);
    if(d.length > 5) return d.slice(0,5)+'-'+d.slice(5);
    return d;
  }
  async function viaCEP(cep){
    var d = (cep||'').replace(/\D/g,'');
    if(d.length !== 8) return null;
    const r = await fetch('https://viacep.com.br/ws/'+d+'/json/');
    const j = await r.json();
    if(j && !j.erro) return j;
    return null;
  }
  document.addEventListener('DOMContentLoaded', function(){
    var cep = document.getElementById('cep');
    if(cep){
      cep.addEventListener('input', function(){ cep.value = maskCEP(cep.value); });
      cep.addEventListener('blur', async function(){
        const data = await viaCEP(cep.value);
        if(data){
          var $ = s => document.getElementById(s);
          $('logradouro').value = data.logradouro || '';
          $('bairro').value = data.bairro || '';
          $('cidade').value = data.localidade || '';
          $('uf').value = (data.uf || '').toUpperCase();
        }
      });
    }
  });
})();
</script>
@endsection
