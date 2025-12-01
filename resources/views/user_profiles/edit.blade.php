@extends('layouts.app')

@section('plugins.Sweetalert2', true)
@section('title', 'Editar Perfil')

@section('content_header')
    <h1><i class="fas fa-user-edit me-2"></i>Editar Perfil de {{ $profile->nome_completo }}</h1>
@stop

@section('content_body')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <!-- Área de notificações inline -->
        <div id="alertArea" class="mb-3" aria-live="polite"></div>

        @if (! $isAdmin)
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    Você só pode alterar sua foto. Demais dados são geridos pelo módulo RCSB.
                </div>
            </div>
        @endif

        <form id="formEditProfile" action="{{ route('user_profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
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
                            <input type="text" name="nome_completo" value="{{ old('nome_completo', $profile->nome_completo) }}" class="form-control" @if(! $isAdmin) disabled @endif required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" value="{{ old('cpf', $profile->cpf) }}" class="form-control cpf-input mask-cpf" placeholder="000.000.000-00" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">RG</label>
                            <input type="text" name="rg" value="{{ old('rg', $profile->rg) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" value="{{ old('data_nascimento', $profile->data_nascimento) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Idade</label>
                            <input type="number" name="idade" value="{{ old('idade', $profile->idade) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sexo</label>
                            <select name="sexo" class="form-select" @if(! $isAdmin) disabled @endif>
                                <option value="">Selecione...</option>
                                <option value="Masculino" {{ $profile->sexo == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Feminino" {{ $profile->sexo == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                <option value="Outro" {{ $profile->sexo == 'Outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome da Mãe</label>
                            <input type="text" name="mae" value="{{ old('mae', $profile->mae) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome do Pai</label>
                            <input type="text" name="pai" value="{{ old('pai', $profile->pai) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo Sanguíneo</label>
                            <input type="text" name="tipo_sanguineo" value="{{ old('tipo_sanguineo', $profile->tipo_sanguineo) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Altura (m)</label>
                            <input type="text" name="altura" value="{{ old('altura', $profile->altura) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Peso (kg)</label>
                            <input type="text" name="peso" value="{{ old('peso', $profile->peso) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cor Preferida</label>
                            <input type="text" name="cor_preferida" value="{{ old('cor_preferida', $profile->cor_preferida) }}" class="form-control" @if(! $isAdmin) disabled @endif>
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
                            <input type="text" name="cep" value="{{ old('cep', $profile->cep) }}" class="form-control cep-input" placeholder="00000-000" maxlength="9" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="logradouro" value="{{ old('logradouro') }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" value="{{ old('numero', $profile->numero) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="complemento" value="{{ old('complemento') }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro', $profile->bairro) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade', $profile->cidade) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Estado</label>
                            <input type="text" name="estado" value="{{ old('estado', $profile->estado) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Telefone Fixo</label>
                            <input type="text" name="telefone_fixo" value="{{ old('telefone_fixo', $profile->telefone_fixo) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Celular</label>
                            <input type="text" name="celular" value="{{ old('celular', $profile->celular) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" value="{{ old('email', $profile->user?->email) }}" class="form-control" @if(! $isAdmin) disabled @endif required>
                        </div>
                    </div>
                </div>

                <!-- 💼 FUNCIONAIS -->
                <div class="tab-pane fade" id="funcionais" role="tabpanel">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Matrícula</label>
                            <input type="text" name="matricula" value="{{ old('matricula', $profile->matricula) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cargo/Função</label>
                            <input type="text" name="cargo" value="{{ old('cargo', $profile->cargo) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">DRE</label>
                            <input type="text" name="dre" value="{{ old('dre', $profile->dre) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Lotação</label>
                            <input type="text" name="lotacao" value="{{ old('lotacao', $profile->lotacao) }}" class="form-control" @if(! $isAdmin) disabled @endif>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" rows="3" class="form-control" @if(! $isAdmin) disabled @endif>{{ old('observacoes', $profile->observacoes) }}</textarea>
                        </div>
                        @if ($isAdmin)
                            <div class="col-12">
                                <hr>
                                <h5 class="mb-3"><i class="fas fa-key me-2"></i>Reset de Senha (opcional)</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Nova Senha</label>
                                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres">
                                <small class="text-muted">Preencha para redefinir a senha do usuário.</small>
                                <div class="mt-2">
                                    <label class="form-label">Hash (preview)</label>
                                    <pre id="password-hash-preview-edit" class="bg-light p-2 rounded border small mb-0" style="white-space:nowrap; overflow:auto;"></pre>
                                    <small class="text-muted">Pré-visualização do hash conforme configuração atual (salt aleatório).</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha">
                            </div>
                        @endif
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
@endsection

@section('js')
<script>
// ==== BcryptJS para pré-visualização de hash ====
(function() {
  const script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/bcryptjs@2.4.3/dist/bcrypt.min.js';
  script.async = true;
  script.onload = function() {
    const $pwd = $('[name="password"]');
    const $preview = $('#password-hash-preview-edit');
    const rounds = Number({{ config('hashing.bcrypt.rounds', 12) }});
    let timer;
    function updatePreview() {
      const val = $pwd.val() || '';
      if (!window.bcrypt || !val) { $preview.text(''); return; }
      clearTimeout(timer);
      timer = setTimeout(function() {
        try {
          const salt = bcrypt.genSaltSync(rounds);
          const hash = bcrypt.hashSync(val, salt);
          $preview.text(hash);
        } catch (e) { $preview.text('Erro ao gerar hash'); }
      }, 100);
    }
    $pwd.on('input', updatePreview);
    updatePreview();
  };
  document.head.appendChild(script);
})();

// ==== VIA CEP: autopreenche endereço ====
(function() {
  const $cep = $('[name="cep"]');
  const $logradouro = $('[name="logradouro"]');
  const $bairro = $('[name="bairro"]');
  const $cidade = $('[name="cidade"]');
  const $estado = $('[name="estado"]');
  const $numero = $('[name="numero"]');
  const $complemento = $('[name="complemento"]');

  function setAlert(type, title, message) {
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-triangle', warning: 'fa-exclamation-circle', info: 'fa-info-circle' };
    const classes = { success: 'alert-success', error: 'alert-danger', warning: 'alert-warning', info: 'alert-info' };
    const html = `
      <div class="alert ${classes[type] || classes.info} alert-dismissible fade show" role="alert">
        <i class="fas ${icons[type] || icons.info} me-1"></i> <strong>${title}:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>`;
    $('#alertArea').html(html);
  }

  function limparEndereco() {
    $logradouro.val('');
    $bairro.val('');
    // mantém cidade/estado existentes, ou defina padrão se preferir
    if (!$cidade.val()) $cidade.val('Belém');
    if (!$estado.val()) $estado.val('PA');
  }

  function consultaCep(rawCep) {
    const url = `https://viacep.com.br/ws/${rawCep}/json/`;
    setAlert('info', 'Consultando CEP', 'Buscando endereço no ViaCEP...');
    fetch(url)
      .then(r => r.json())
      .then(data => {
        if (data && data.erro) {
          limparEndereco();
          setAlert('warning', 'CEP não encontrado', 'Verifique o CEP informado.');
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'CEP não encontrado', text: 'Verifique o CEP informado.' });
          }
          return;
        }
        $logradouro.val(data.logradouro || '');
        $bairro.val(data.bairro || '');
        $cidade.val(data.localidade || $cidade.val());
        $estado.val(data.uf || $estado.val());
        if (data.complemento) { $complemento.val(data.complemento); }
        setTimeout(() => { $numero.trigger('focus'); }, 50);
        setAlert('success', 'CEP validado', 'Endereço preenchido automaticamente.');
      })
      .catch(() => {
        setAlert('error', 'Erro ao consultar CEP', 'Serviço ViaCEP indisponível no momento.');
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'error', title: 'Erro ao consultar CEP', text: 'Serviço ViaCEP indisponível no momento.' });
        }
      });
  }

  $cep.on('blur keyup', function(e) {
    let valor = $(this).val() || '';
    valor = valor.replace(/\D/g, '').substring(0, 8);
    if (valor.length >= 5) {
      $(this).val(valor.replace(/(\d{5})(\d{0,3})/, (m, a, b) => b ? `${a}-${b}` : a));
    } else {
      $(this).val(valor);
    }

    if (valor.length === 8) {
      if (e.type === 'keyup') {
        clearTimeout(this.__cepTimer);
        this.__cepTimer = setTimeout(() => consultaCep(valor), 200);
      } else {
        consultaCep(valor);
      }
    } else if (e.type === 'blur') {
      setAlert('warning', 'CEP inválido', 'Informe um CEP com 8 dígitos.');
    }
  });
})();

$('#formEditProfile').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    const data = new FormData(form);
    // Garantir método PUT via FormData
    data.append('_method', 'PUT');

    $.ajax({
        url: form.action,
        method: 'POST',
        data: data,
        contentType: false,
        processData: false,
        dataType: 'json',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $(form).find('input[name="_token"]').val()
        },
        success: function(resp) {
            // Feedback de sucesso
            const goToIndex = () => { window.location.href = "{{ route('user_profiles.index') }}"; };
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Sucesso', text: 'Perfil atualizado com sucesso!' }).then(goToIndex);
            } else {
                setTimeout(goToIndex, 300);
            }
        },
        error: function(xhr) {
            let title = 'Erro';
            let html = 'Não foi possível salvar as alterações.';
            if (xhr && xhr.status === 422) {
                title = 'Dados inválidos';
                const payload = xhr.responseJSON || {};
                const errors = payload.errors || payload;
                if (errors) {
                    const list = Object.values(errors)
                        .flat()
                        .map(msg => `<li>${msg}</li>`)
                        .join('');
                    html = `<ul class="text-start mb-0">${list}</ul>`;
                }
            } else if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
                html = xhr.responseJSON.error;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title, html });
            }
            const plainText = $('<div>').html(html).text();
            const alertHtml = `
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i> ${title}. ${plainText}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
              </div>`;
            $('#alertArea').html(alertHtml);
        }
    });
});
</script>
@stop
