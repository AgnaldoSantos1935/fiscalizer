@extends('layouts.app')
@section('title', 'Nova Escola')

@section('content')
@section('breadcrumb')
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item"><a href="{{ route('escolas.index') }}" class="text-decoration-none text-primary fw-semibold"><i class="fas fa-school me-1"></i> Escolas</a></li>
      <li class="breadcrumb-item active text-secondary fw-semibold">Nova Escola</li>
    </ol>
  </nav>
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
            console.error(error);
        });
    });
});
</script>
@endsection
