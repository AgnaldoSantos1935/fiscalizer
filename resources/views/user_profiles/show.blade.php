@extends('layouts.app')

@section('title', 'Perfil do Usuário')

@section('content_header')
    <h1>
        <i class="fas fa-id-card me-2"></i>Perfil de {{ $profile->nome_completo }}
    </h1>
@stop

@section('content_body')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                @if ($profile->foto)
                    <img src="{{ asset('storage/'.$profile->foto) }}" class="img-thumbnail rounded-circle shadow-sm" width="180" alt="Foto do usuário">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($profile->nome_completo) }}&size=180&background=0D8ABC&color=fff"
                         class="img-thumbnail rounded-circle shadow-sm" alt="Avatar gerado">
                @endif
                <div class="mt-3">
                    <span class="badge bg-primary"><i class="fas fa-briefcase me-1"></i>{{ $profile->cargo ?? '—' }}</span><br>
                    <small class="text-muted">{{ $profile->user?->email }}</small>
                </div>
            </div>

            <div class="col-md-9">
                <h4 class="fw-semibold text-primary mb-3"><i class="fas fa-user me-1"></i>Dados Pessoais</h4>
                <div class="row">
                    <div class="col-md-4 mb-2"><strong>CPF:</strong> {{ $profile->cpf }}</div>
                    <div class="col-md-4 mb-2"><strong>RG:</strong> {{ $profile->rg ?? '—' }}</div>
                    <div class="col-md-4 mb-2"><strong>Data Nasc.:</strong> {{ $profile->data_nascimento ? \Carbon\Carbon::parse($profile->data_nascimento)->format('d/m/Y') : '—' }}</div>
                    <div class="col-md-3 mb-2"><strong>Idade:</strong> {{ $profile->idade ?? '—' }}</div>
                    <div class="col-md-3 mb-2"><strong>Sexo:</strong> {{ $profile->sexo ?? '—' }}</div>
                    <div class="col-md-3 mb-2"><strong>Tipo Sanguíneo:</strong> {{ $profile->tipo_sanguineo ?? '—' }}</div>
                    <div class="col-md-3 mb-2"><strong>Altura:</strong> {{ $profile->altura ? number_format($profile->altura, 2, ',', '.') . ' m' : '—' }}</div>
                    <div class="col-md-3 mb-2"><strong>Peso:</strong> {{ $profile->peso ? $profile->peso . ' kg' : '—' }}</div>
                    <div class="col-md-6 mb-2"><strong>Mãe:</strong> {{ $profile->mae ?? '—' }}</div>
                    <div class="col-md-6 mb-2"><strong>Pai:</strong> {{ $profile->pai ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🏠 Endereço -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-primary"><i class="fas fa-home me-1"></i>Endereço e Contato</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-2"><strong>Endereço:</strong>
                @php
                    $parts = array_filter([
                        $profile->logradouro ?? null,
                        $profile->numero ?? null,
                        $profile->complemento ?? null,
                        $profile->bairro ?? null,
                    ]);
                    $texto = $parts ? implode(', ', $parts) : ($profile->endereco ? ($profile->endereco . ($profile->numero ? ', '.$profile->numero : '')) : '—');
                @endphp
                {{ $texto }}
            </div>
            <div class="col-md-6 mb-2"><strong>Bairro:</strong> {{ $profile->bairro ?? '—' }}</div>
            <div class="col-md-4 mb-2"><strong>CEP:</strong> {{ $profile->cep ?? '—' }}</div>
            <div class="col-md-4 mb-2"><strong>Cidade:</strong> {{ $profile->cidade ?? '—' }}</div>
            <div class="col-md-4 mb-2"><strong>Estado:</strong> {{ $profile->estado ?? '—' }}</div>
            <div class="col-md-4 mb-2"><strong>Telefone:</strong> {{ $profile->telefone_fixo ?? '—' }}</div>
            <div class="col-md-4 mb-2"><strong>Celular:</strong> {{ $profile->celular ?? '—' }}</div>
            <div class="col-md-4 mb-2"><strong>E-mail:</strong> {{ $profile->user?->email ?? '—' }}</div>
        </div>
    </div>
</div>

<!-- 💼 Dados Funcionais -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-primary"><i class="fas fa-briefcase me-1"></i>Dados Funcionais</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-2"><strong>Matrícula:</strong> {{ $profile->matricula ?? '—' }}</div>
            <div class="col-md-3 mb-2"><strong>Cargo:</strong> {{ $profile->cargo ?? '—' }}</div>
            <div class="col-md-3 mb-2"><strong>DRE:</strong> {{ $profile->dre ?? '—' }}</div>
            <div class="col-md-3 mb-2"><strong>Lotação:</strong> {{ $profile->lotacao ?? '—' }}</div>
            <div class="col-md-6 mb-2"><strong>E-mail:</strong> {{ $profile->user?->email ?? '—' }}</div>
        </div>

        @if ($profile->observacoes)
            <div class="mt-3">
                <strong>Observações:</strong><br>
                <p class="text-muted mb-0">{{ $profile->observacoes }}</p>
            </div>
        @endif
    </div>
</div>

<!-- 🔙 Botão de Voltar -->
<div class="text-end">
    <a href="{{ route('user_profiles.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
    <a href="{{ route('user_profiles.edit', $profile->id) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i>{{ ($isAdmin ?? false) ? 'Editar' : 'Atualizar Foto' }}
    </a>
</div>
@endsection
