@extends('layouts.app')
@section('title', 'Detalhes da Empresa')

@section('content')
<div class="container-fluid">

    <!-- 🔹 Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-secondary fw-semibold mb-0">
            <i class="fas fa-eye text-info me-2"></i>Detalhes da Empresa
        </h4>
        <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar à lista
        </a>
    </div>

    <!-- 🔹 Card principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body bg-white">

            <table class="table table-borderless align-middle">
                <tbody>
                    <tr>
                        <th class="text-secondary small" style="width: 20%;">Razão Social:</th>
                        <td class="fw-semibold">{{ $empresa->razao_social }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Nome Fantasia:</th>
                        <td>{{ $empresa->nome_fantasia ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">CNPJ:</th>
                        <td>{{ $empresa->cnpj }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Inscrição Estadual:</th>
                        <td>{{ $empresa->inscricao_estadual ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">E-mail:</th>
                        <td>{{ $empresa->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Telefone:</th>
                        <td>{{ $empresa->telefone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Endereço:</th>
                        <td>{{ $empresa->endereco ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Cidade:</th>
                        <td>{{ $empresa->cidade ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">UF:</th>
                        <td>{{ $empresa->uf ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">CEP:</th>
                        <td>{{ $empresa->cep ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Criado em:</th>
                        <td>{{ $empresa->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="text-secondary small">Última atualização:</th>
                        <td>{{ $empresa->updated_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-4 text-end">
                <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-warning text-white btn-sm px-3 me-2">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a href="{{ route('empresas.index') }}" class="btn btn-secondary btn-sm px-3">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.table th {
    width: 25%;
    white-space: nowrap;
}
.table td {
    color: #333;
}
.card {
    border: none !important;
}
</style>
@endsection
