@extends('layouts.app')
@section('title','Nova Ata')
@section('content')
<div class="container-fluid">
  @section('breadcrumb')
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
        <li class="breadcrumb-item">
          <a href="{{ route('atas.index') }}" class="text-decoration-none text-primary fw-semibold">
            <i class="fas fa-file-alt me-1"></i> Atas
          </a>
        </li>
        <li class="breadcrumb-item active text-secondary fw-semibold">Nova Ata</li>
      </ol>
    </nav>
  @endsection
  <h3 class="mb-3">Cadastrar Ata de Registro de Preços</h3>
  <form method="POST" action="{{ route('atas.store') }}" class="card p-3">
    @csrf
    <div class="row g-3">
      <div class="col-md-3"><label class="form-label">Número</label><input type="text" name="numero" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">Processo</label><input type="text" name="processo" class="form-control"></div>
      <div class="col-md-6"><label class="form-label">Objeto</label><input type="text" name="objeto" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Órgão Gerenciador</label>
        <select name="orgao_gerenciador_id" class="form-select" required>
          <option value="">Selecione...</option>
          @foreach($empresas as $e)<option value="{{ $e->id }}">{{ $e->razao_social }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-6"><label class="form-label">Fornecedor</label>
        <select name="fornecedor_id" class="form-select" required>
          <option value="">Selecione...</option>
          @foreach($empresas as $e)<option value="{{ $e->id }}">{{ $e->razao_social }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-3"><label class="form-label">Publicação</label><input type="date" name="data_publicacao" class="form-control"></div>
      <div class="col-md-3"><label class="form-label">Início Vigência</label><input type="date" name="vigencia_inicio" class="form-control"></div>
      <div class="col-md-3"><label class="form-label">Fim Vigência</label><input type="date" name="vigencia_fim" class="form-control"></div>
      <div class="col-md-3"><label class="form-label">Prorrogação (meses)</label><input type="number" name="prorroga_total_meses" class="form-control" min="0"></div>
      <div class="col-md-3"><label class="form-label">Saldo Global</label><input type="text" name="saldo_global" class="form-control money-br-input"></div>
    </div>
    <div class="mt-3 d-flex gap-2 justify-content-end">
      <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Salvar</button>
      <a href="{{ route('atas.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
    </div>
  </form>
</div>
@endsection
