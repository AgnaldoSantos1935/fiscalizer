@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content_header')
    <h1><i class="fas fa-user-edit me-2"></i>Editar Perfil de {{ $profile->nome_completo }}</h1>
@stop

@section('content')
@include('layouts.components.breadcrumbs')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <form action="{{ route('user_profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <!-- 🔹 Abas -->
            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pessoais-tab" data-bs-toggle="tab" data-bs-target="#pessoais" type="button" role="tab">
                        <i class="fas fa-user me-1"></i>Dados Pessoais
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="endereco-tab" data-bs-toggle="tab" data-bs-target="#endereco" type="button" role="tab">
                        <i class="fas fa-home me-1"></i>Endereço e Contato
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="funcionais-tab" data-bs-toggle="tab" data-bs-target="#funcionais" type="button" role="tab">
                        <i class="fas fa-briefcase me-1"></i>Dados Funcionais
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">

                <!-- 🧍 DADOS PESSOAIS -->
                <div class="tab-pane fade show active" id="pessoais" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome_completo" value="{{ old('nome_completo', $profile->nome_completo) }}" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CPF</label>
<input type="text" name="cpf" value="{{ old('cpf', $profile->cpf) }}" class="form-control cpf-input mask-cpf" placeholder="000.000.000-00">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RG</label>
                            <input type="text" name="rg" value="{{ old('rg', $profile->rg) }}" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $profile->data_nascimento) }}" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Idade</label>
                            <input type="number" name="idade" value="{{ old('idade', $profile->idade) }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sexo</label>
                            <select name="sexo" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="Masculino" {{ $profile->sexo == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Feminino" {{ $profile->sexo == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                <option value="Outro" {{ $profile->sexo == 'Outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Signo</label>
                            <input type="text" name="signo" value="{{ old('signo', $profile->signo) }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome da Mãe</label>
                            <input type="text" name="mae" value="{{ old('mae', $profile->mae) }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Pai</label>
                            <input type="text" name="pai" value="{{ old('pai', $profile->pai) }}" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo Sanguíneo</label>
                            <input type="text" name="tipo_sanguineo" value="{{ old('tipo_sanguineo', $profile->tipo_sanguineo) }}" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Altura (m)</label>
                            <input type="text" name="altura" value="{{ old('altura', $profile->altura) }}" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="text" name="peso" value="{{ old('peso', $profile->peso) }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cor Preferida</label>
                            <input type="text" name="cor_preferida" value="{{ old('cor_preferida', $profile->cor_preferida) }}" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
                            @if ($profile->foto)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$profile->foto) }}" class="rounded shadow-sm" width="120" alt="Foto atual">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 🏠 ENDEREÇO -->
                <div class="tab-pane fade" id="endereco" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep', $profile->cep) }}" class="form-control cep-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="logradouro" value="{{ old('logradouro') }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" value="{{ old('numero', $profile->numero) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complemento" value="{{ old('complemento') }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro', $profile->bairro) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade', $profile->cidade) }}" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="estado" value="{{ old('estado', $profile->estado) }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telefone Fixo</label>
                            <input type="text" name="telefone_fixo" value="{{ old('telefone_fixo', $profile->telefone_fixo) }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" value="{{ old('celular', $profile->celular) }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">E-mail Pessoal</label>
                            <input type="email" name="email_pessoal" value="{{ old('email_pessoal', $profile->email_pessoal) }}" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- 💼 FUNCIONAIS -->
                <div class="tab-pane fade" id="funcionais" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Matrícula</label>
                            <input type="text" name="matricula" value="{{ old('matricula', $profile->matricula) }}" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cargo/Função</label>
                            <input type="text" name="cargo" value="{{ old('cargo', $profile->cargo) }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">DRE</label>
                            <input type="text" name="dre" value="{{ old('dre', $profile->dre) }}" class="form-control">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Lotação</label>
                            <input type="text" name="lotacao" value="{{ old('lotacao', $profile->lotacao) }}" class="form-control">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">E-mail Institucional</label>
                            <input type="email" name="email_institucional" value="{{ old('email_institucional', $profile->email_institucional) }}" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" rows="3" class="form-control">{{ old('observacoes', $profile->observacoes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Salvar Alterações
                </button>
                <a href="{{ route('user_profiles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </form>
    </div>
</div>
@stop
