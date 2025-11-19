@extends('layouts.app')

@section('content')
@include('layouts.components.breadcrumbs')
<div class="container mt-5">
    <h2 class="mb-4">Painel de Controle - Fiscalizer</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 mb-3">
                <h5>Empresas</h5>

            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 mb-3">
                <h5>Contratos</h5>

            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3 mb-3">
                <h5>Medições</h5>

            </div>
        </div>
    </div>
</div>
@endsection

