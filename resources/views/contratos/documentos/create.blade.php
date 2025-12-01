@extends('layouts.app')
@section('title', 'Cadastrar Documento do Contrato')

@section('breadcrumb')
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item">
        <a href="{{ route('contratos.show', $contrato->id) }}" class="text-decoration-none text-primary fw-semibold">
          <i class="fas fa-file-contract me-1"></i> Voltar ao Contrato
        </a>
      </li>
      <li class="breadcrumb-item active text-secondary fw-semibold">
        Cadastrar Documento do Contrato nº {{ $contrato->numero }}
      </li>
    </ol>
  </nav>
@endsection
@section('content_body')
<div class="container">

  <div class="card mb-3">
    <div class="card-header">
      <strong>Dados do Contrato</strong>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <dl class="row mb-0">
            <dt class="col-sm-4">Número</dt>
            <dd class="col-sm-8">{{ $contrato->numero ?? '—' }}</dd>
            <dt class="col-sm-4">Objeto</dt>
            <dd class="col-sm-8">{{ $contrato->objeto ?? '—' }}</dd>
            <dt class="col-sm-4">Contratada</dt>
            <dd class="col-sm-8">{{ optional($contrato->contratada)->razao_social ?? ($contrato->empresa_razao_social ?? '—') }}</dd>
            <dt class="col-sm-4">CNPJ</dt>
            <dd class="col-sm-8">{{ optional($contrato->contratada)->cnpj ?? ($contrato->empresa_cnpj ?? '—') }}</dd>
          </dl>
        </div>
        <div class="col-md-6">
          <dl class="row mb-0">
            <dt class="col-sm-4">Início</dt>
            <dd class="col-sm-8">{{ optional($contrato->data_inicio ?? $contrato->data_inicio_vigencia ?? $contrato->data_assinatura)->format('d/m/Y') ?? '—' }}</dd>
            <dt class="col-sm-4">Fim</dt>
            <dd class="col-sm-8">{{ optional($contrato->data_fim)->format('d/m/Y') ?? '—' }}</dd>
            <dt class="col-sm-4">Valor Global</dt>
            <dd class="col-sm-8">{{ $contrato->valor_global !== null ? ('R$ ' . number_format((float)$contrato->valor_global, 2, ',', '.')) : '—' }}</dd>
          </dl>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Cadastrar Documento Vinculado</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('contratos.pdf.upload', $contrato->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Tipo do Documento</label>
            <select name="documento_tipo_id" id="documento_tipo_id" class="form-select" required>
              <option value="">Selecione...</option>
              @foreach(($tipos ?? []) as $tipo)
                <option value="{{ $tipo->id }}" data-permite-nova-data-fim="{{ $tipo->permite_nova_data_fim ? '1' : '0' }}">
                  {{ $tipo->nome }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-8 mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" placeholder="Ex.: Termo Aditivo nº 1" />
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" rows="2" class="form-control" placeholder="Breve descrição do documento"></textarea>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3" id="campo_nova_data_fim" style="display:none;">
            <label class="form-label">Nova Data de Fim da Vigência</label>
            <input type="date" name="nova_data_fim" class="form-control" />
            <small class="text-muted">Disponível apenas quando o tipo permite.</small>
          </div>
          <div class="col-md-8 mb-3">
            <label class="form-label">Arquivo PDF</label>
            <input type="file" name="pdf" accept="application/pdf" class="form-control" required />
          </div>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Salvar Documento
          </button>
          <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Cancelar
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  (function(){
    const select = document.getElementById('documento_tipo_id');
    const campo = document.getElementById('campo_nova_data_fim');
    function toggleCampo(){
      const opt = select.options[select.selectedIndex];
      const permite = opt && opt.getAttribute('data-permite-nova-data-fim') === '1';
      campo.style.display = permite ? '' : 'none';
      if (!permite) {
        const input = campo.querySelector('input[name="nova_data_fim"]');
        if (input) input.value = '';
      }
    }
    select && select.addEventListener('change', toggleCampo);
    toggleCampo();
  })();
</script>
@endsection
