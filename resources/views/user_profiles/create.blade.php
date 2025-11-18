@extends('layouts.app')

@section('title', 'Novo Perfil')

@section('content_header')
    <h1><i class="fas fa-user-plus me-2"></i>Cadastrar Novo Perfil</h1>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">

        <form id="formCreateProfile" action="{{ route('user_profiles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="pessoais-tab" data-bs-toggle="tab" data-bs-target="#pessoais" type="button" role="tab">
                        <i class="fas fa-user me-1"></i>Dados Pessoais
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="endereco-tab" data-bs-toggle="tab" data-bs-target="#endereco" type="button" role="tab">
                        <i class="fas fa-home me-1"></i>Endereço e Contato
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="funcionais-tab" data-bs-toggle="tab" data-bs-target="#funcionais" type="button" role="tab">
                        <i class="fas fa-briefcase me-1"></i>Dados Funcionais
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- 🧍 PESSOAIS -->
                <div class="tab-pane fade show active" id="pessoais" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome_completo" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RG</label>
                            <input type="text" name="rg" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data Nascimento</label>
                            <input type="date" name="data_nascimento" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sexo</label>
                            <select name="sexo" class="form-select">
                                <option value="">Selecione...</option>
                                <option>Masculino</option>
                                <option>Feminino</option>
                                <option>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Signo</label>
                            <input type="text" name="signo" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo Sanguíneo</label>
                            <input type="text" name="tipo_sanguineo" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Altura (m)</label>
                            <input type="text" name="altura" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="text" name="peso" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cor Preferida</label>
                            <input type="text" name="cor_preferida" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- 🏠 ENDEREÇO -->
                <div class="tab-pane fade" id="endereco" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CEP</label>
                            <input type="text" name="cep" class="form-control cep-input">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="logradouro" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complemento" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" value="Belém" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="estado" value="PA" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telefone Fixo</label>
                            <input type="text" name="telefone_fixo" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">E-mail Pessoal</label>
                            <input type="email" name="email_pessoal" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- 💼 FUNCIONAIS -->
                <div class="tab-pane fade" id="funcionais" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Matrícula</label>
                            <input type="text" name="matricula" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cargo/Função</label>
                            <input type="text" name="cargo" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">DRE</label>
                            <input type="text" name="dre" class="form-control">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Lotação</label>
                            <input type="text" name="lotacao" class="form-control">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">E-mail Institucional</label>
                            <input type="email" name="email_institucional" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Salvar
                </button>
                <a href="{{ route('user_profiles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
$('#formCreateProfile').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    const data = new FormData(form);

    $.ajax({
        url: form.action,
        method: 'POST',
        data: data,
        contentType: false,
        processData: false,
        success: function(resp) {
            if (resp.success) {
                Swal.fire('Sucesso', 'Perfil criado com sucesso!', 'success');
                $('#tabelaPerfis').DataTable().ajax.reload();
                form.reset();
            }
        },
        error: function(err) {
            Swal.fire('Erro', 'Não foi possível salvar o perfil.', 'error');
        }
    });
});
</script>
@stop
