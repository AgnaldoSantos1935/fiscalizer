@extends('layouts.app')

@section('title', 'Editar Contrato')

@section('breadcrumb')
    @include('layouts.components.breadcrumbs', [
        'trail' => [
            ['label' => 'Contratos', 'icon' => 'fas fa-file-contract', 'url' => route('contratos.index')],
            ['label' => 'Editar Contrato']
        ]
    ])
@endsection

@section('content_body')

<div class="container-fluid">

    <h2 class="mb-4 fw-semibold text-primary">
        <i class="fas fa-file-signature me-2"></i>
        Editar Contrato
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('contratos.update', $contrato->id) }}"
          method="POST"
          class="card shadow-sm p-4 rounded-4"
          id="formContratoEdit">

        @csrf
        @method('PUT')

        <!-- Dados principais -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <label class="form-label fw-semibold">Número *</label>
                <input type="text" name="numero" class="form-control"
                       value="{{ $contrato->numero }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Valor Global (R$)</label>
                <input type="text" name="valor_global" class="form-control money-br-input"
                       value="{{ $contrato->valor_global ? number_format($contrato->valor_global,2,',','.') : '' }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Empresa Contratada *</label>

                <!-- SOMENTE EXIBE, NÃO PERMITE TROCAR -->
                <input type="text" class="form-control bg-light"
                       value="{{ optional($contrato->contratada)->razao_social ?: '—' }}"
                       disabled>

                <input type="hidden" name="contratada_id" value="{{ $contrato->contratada_id }}">
            </div>
        </div>

        <!-- Objeto -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Objeto *</label>
            <textarea name="objeto" class="form-control" rows="3" required>
{{ $contrato->objeto }}</textarea>
        </div>

        <!-- Itens -->
        <h4 class="mt-4 mb-3 fw-semibold text-secondary">
            <i class="fas fa-boxes me-2"></i>Itens Contratados
        </h4>

        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label class="form-label">Descrição</label>
                <input type="text" id="item_desc_e" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">Unidade</label>
                <input type="text" id="item_unid_e" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">Quantidade</label>
                <input type="number" id="item_qtd_e" min="0" step="0.01" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">Meses</label>
                <input type="number" id="item_meses_e" min="0" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">V.Unitário (R$)</label>
                <input type="text" id="item_vu_e" class="form-control money-br-input">
            </div>

            <div class="col-md-2 mt-3">
                <button type="button" id="addItemE" class="btn btn-primary w-100">
                    <i class="fas fa-plus-circle me-1"></i>Adicionar
                </button>
            </div>
        </div>

        <div class="table-responsive border rounded-3 p-2 bg-white">
            <table class="table table-striped table-sm align-middle" id="itemsTableE">
                <thead class="table-light">
                    <tr>
                        <th>Descrição</th>
                        <th>Unidade</th>
                        <th>Qtd</th>
                        <th>Meses</th>
                        <th>Valor Unit.</th>
                        <th>Aliq. (%)</th>
                        <th>Desc. (%)</th>
                        <th>Total (R$)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="itemsTableBodyE"></tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-end fw-semibold">Total:</th>
                        <th id="itemsTotalBRE" class="fw-bold text-success">R$ 0,00</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <textarea name="itens_fornecimento" id="itens_fornecimento_e" class="d-none"></textarea>

        <div class="text-end mt-4">
            <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Cancelar
            </a>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Salvar Alterações
            </button>
        </div>

    </form>

</div>

@endsection

@section('js')

@endsection
