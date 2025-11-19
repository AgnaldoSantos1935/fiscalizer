@extends('layouts.app')

@section('title', "Editar Contrato {$contrato->numero}")

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="fas fa-file-contract text-primary"></i>
        Editar Contrato – {{ $contrato->numero }}
    </h3>

    <div class="mb-3">
        <a href="{{ route('contratos.pdf', $contrato->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-pdf"></i> Ver PDF do Contrato
        </a>
    </div>

    <form action="{{ route('contratos.update', $contrato->id) }}" method="POST">
        @csrf
        @method('PUT')

        <ul class="nav nav-tabs" id="contratoTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados-gerais" type="button">
                    Dados Gerais
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#empresa" type="button">
                    Empresa
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vigencia-valores" type="button">
                    Vigência & Valores
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#clausulas" type="button">
                    Cláusulas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#obrigacoes" type="button">
                    Obrigações & Itens
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#anexos-riscos" type="button">
                    Anexos & Riscos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fiscais" type="button">
                    Fiscais & Gestor
                </button>
            </li>
        </ul>

        <div class="tab-content border-start border-end border-bottom p-3 bg-white">

            {{-- Dados gerais --}}
            <div class="tab-pane fade show active" id="dados-gerais">
                @include('contratos.partials.tab_dados_gerais')
            </div>

            {{-- Empresa --}}
            <div class="tab-pane fade" id="empresa">
                @include('contratos.partials.tab_empresa')
            </div>

            {{-- Vigência & Valores --}}
            <div class="tab-pane fade" id="vigencia-valores">
                @include('contratos.partials.tab_vigencia_valores')
            </div>

            {{-- Cláusulas --}}
            <div class="tab-pane fade" id="clausulas">
                @include('contratos.partials.tab_clausulas')
            </div>

            {{-- Obrigações & Itens --}}
            <div class="tab-pane fade" id="obrigacoes">
                @include('contratos.partials.tab_obrigacoes_itens')
            </div>

            {{-- Anexos & Riscos --}}
            <div class="tab-pane fade" id="anexos-riscos">
                @include('contratos.partials.tab_anexos_riscos')
            </div>

            {{-- Fiscais --}}
            <div class="tab-pane fade" id="fiscais">
                @include('contratos.partials.tab_fiscais', ['pessoas' => $pessoas])
            </div>

        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar alterações
            </button>
        </div>

    </form>
</div>
@endsection
