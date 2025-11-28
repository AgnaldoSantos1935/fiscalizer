@extends('adminlte::page')

@section('title', 'Gerar Relatório')

@section('content_header')
    <h1><i class="fas fa-file-alt"></i> Gerar Relatório</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('relatorios.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-control" required>
                    <option value="contrato">Contrato</option>
                    <option value="medicao">Medição</option>
                    <option value="empresa">Empresa</option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-3">
                <label class="form-label">Busca</label>
                <input type="text" name="filtros[busca]" class="form-control" placeholder="Buscar título...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data Início</label>
                <input type="date" name="filtros[data_inicio]" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data Fim</label>
                <input type="date" name="filtros[data_fim]" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo (filtro)</label>
                <select name="filtros[tipo]" class="form-control">
                    <option value="">--</option>
                    <option value="contrato">Contrato</option>
                    <option value="medicao">Medição</option>
                    <option value="empresa">Empresa</option>
                </select>
            </div>

            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="{{ route('relatorios.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <a href="{{ route('relatorios.index') }}" class="btn btn-link">Voltar</a>
    </div>
    </div>
@stop

