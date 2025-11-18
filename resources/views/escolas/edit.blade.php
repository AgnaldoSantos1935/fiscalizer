@extends('layouts.app')
@section('title', 'Editar Escola')

@section('content')
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
                    <input type="text" name="cep" value="{{ old('cep', $escola->Cep ?? '') }}" class="form-control form-control-sm cep-input">
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
            console.error(error);
        });
    });
});
</script>
@endsection
