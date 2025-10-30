@extends('layouts.app')
@section('title', 'Nova Empresa')

@section('content')
<div class="container-fluid">

    <!-- 🔹 Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-secondary fw-semibold mb-0">
            <i class="fas fa-plus-circle text-primary me-2"></i>Cadastro de Nova Empresa
        </h4>
        <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar à lista
        </a>
    </div>

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
                               class="form-control form-control-sm @error('cnpj') is-invalid @enderror"
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

                    <div class="col-md-5">
                        <label for="endereco" class="form-label fw-semibold small text-secondary">Endereço</label>
                        <input type="text" name="endereco" id="endereco"
                               class="form-control form-control-sm"
                               value="{{ old('endereco') }}">
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

                    <div class="col-md-2">
                        <label for="cep" class="form-label fw-semibold small text-secondary">CEP</label>
                        <input type="text" name="cep" id="cep"
                               class="form-control form-control-sm"
                               value="{{ old('cep') }}">
                    </div>
                </div>

                <!-- 🔹 Rodapé -->
                <div class="mt-4 text-end">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-3">
                        <i class="fas fa-save me-1"></i> Salvar Empresa
                    </button>
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
