@extends('layouts.app')

@section('title', 'Nova Medição')

@section('content')
<div class="container-fluid">
    @section('breadcrumb')
      <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
              <li class="breadcrumb-item">
                  <a href="{{ route('contratos.index') }}" class="text-decoration-none text-primary fw-semibold">
                      <i class="fas fa-file-contract me-1"></i> Contratos
                  </a>
              </li>
              <li class="breadcrumb-item">
                  <a href="{{ route('contratos.show', $contrato->id) }}" class="text-decoration-none text-primary fw-semibold">
                      Contrato {{ $contrato->numero }}
                  </a>
              </li>
              <li class="breadcrumb-item active text-secondary fw-semibold">Nova Medição</li>
          </ol>
      </nav>
    @endsection
    <h3 class="mb-3">Nova Medição – Contrato {{ $contrato->numero }}</h3>

    <form action="{{ route('contratos.medicoes.store', $contrato->id) }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Competência (MM/AAAA)</label>
                <input type="text" name="competencia" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Tipo de medição</label>
                <select name="tipo" class="form-select">
                    <option value="software"    {{ $tipo=='software'?'selected':'' }}>Fábrica de Software</option>
                    <option value="telco"       {{ $tipo=='telco'?'selected':'' }}>Internet / Links (Telco)</option>
                    <option value="fixo_mensal" {{ $tipo=='fixo_mensal'?'selected':'' }}>Valor Mensal Fixo</option>
                </select>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="tipoTabs">
            <li class="nav-item">
                <button class="nav-link {{ $tipo=='software'?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tabSoftware" type="button">
                    Software (PF/UST)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $tipo=='telco'?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tabTelco" type="button">
                    Telco (Internet/Links)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $tipo=='fixo_mensal'?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tabFixo" type="button">
                    Mensal Fixo
                </button>
            </li>
        </ul>

        <div class="tab-content border p-3 bg-white rounded-3">
            <div class="tab-pane fade {{ $tipo=='software'?'show active':'' }}" id="tabSoftware">
                @include('medicoes.partials.form_software')
            </div>
            <div class="tab-pane fade {{ $tipo=='telco'?'show active':'' }}" id="tabTelco">
                @include('medicoes.partials.form_telco')
            </div>
            <div class="tab-pane fade {{ $tipo=='fixo_mensal'?'show active':'' }}" id="tabFixo">
                @include('medicoes.partials.form_fixo')
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end gap-2">
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Medição
            </button>
            <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
