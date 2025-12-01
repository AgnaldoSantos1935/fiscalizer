@extends('layouts.app')
@section('title', 'Informações do Contrato')

@section('content_body')
@section('breadcrumb')
    <!-- 🔹 Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
            <li class="breadcrumb-item">
                <a href="{{ route('contratos.index') }}" class="text-decoration-none text-primary fw-semibold">
                    <i class="fas fa-file-contract me-1"></i> Contratos
                </a>
            </li>
            <li class="breadcrumb-item active text-secondary fw-semibold" id="breadcrumbContrato">
                Carregando...
            </li>
            @if(request()->get('from') === 'empenhos.create')
                <li class="breadcrumb-item">
                    <span class="text-secondary">Empenhos</span>
                </li>
                <li class="breadcrumb-item active text-secondary fw-semibold">
                    Cadastrar Nota de Empenho
                </li>
            @endif
        </ol>
    </nav>
@endsection
<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <!-- 🔹 Alerta de erro de carregamento -->
    <div id="alertErroContrato" class="mb-3"></div>

    <!-- 🔹 Nova Vigência (se houver aditivo de prazo) -->
    <div id="alertNovaVigencia" class="mb-3"></div>



 <!-- 🔹 Resumo Financeiro -->
  <div class="card card">
              <div class="card-header">
                <div class="card-title">
                 <h5 class="mb-0 text-with" id="tituloContrato">
                   Contrato nº {{ $contrato->numero }} — {{ optional($contrato->contratada)->razao_social ?? '—' }}
                 </h5>
                     </div>
 <div class="card-tools">

                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div><!-- /.card-header -->
              <div class="card-body">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-3">
                 <div class="small-box bg-success"  data-bs-toggle="tooltip" title="Valor global do contrato por ano">
              <div class="inner">
              <h4 id="resumoGlobal">{{ $totais['valor_global_br'] ?? ('R$ ' . number_format((float) ($totais['valor_global'] ?? 0), 2, ',', '.')) }}</h4>
               <p></p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Valor Global por ano</a>
            </div><!---->
          </div>
            <div class="col-lg-3 col-3">
                             <div class="small-box bg-success"  data-bs-toggle="tooltip" title="Valor total empenhado até o momento">
              <div class="inner">
                <h4 id="resumoEmpenhado">{{ $totais['valor_empenhado_br'] ?? ('R$ ' . number_format((float) ($totais['valor_empenhado'] ?? 0), 2, ',', '.')) }}</h4>
                  <p></p>

              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer"> Valores Empenhados</a>
            </div><!---->
          </div>
            <div class="col-lg-3 col-3">
                  <div class="small-box bg-warning"  data-bs-toggle="tooltip" title="Total da soma dos pagamentos informados até o momento">
              <div class="inner">
                <h4 id="resumoPago">{{ $totais['valor_pago_br'] ?? ('R$ ' . number_format((float) ($totais['valor_pago'] ?? 0), 2, ',', '.')) }}</h4>
                <p></p>

              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Valores Pagos</i></a>
            </div><!---->
          </div>
               <div class="col-lg-3 col-3">
                <div class="small-box bg-danger" data-bs-toggle="tooltip" title="Saldo da somas dos valores empenhados">
              <div class="inner">
                 <h4 id="resumoSaldo">{{ $totais['saldo_br'] ?? ('R$ ' . number_format((float) ($totais['saldo'] ?? 0), 2, ',', '.')) }}</h4>
                <p></p>

              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">Saldo Disponível</i></a>
            </div><!---->
          </div>
          </div>
          </div>

    <!-- final do Resumo Financeiro -->
  </div>
 </div>

    <!-- 🔹 Detalhes do Contrato -->
    <div class="row g-3 align-items-stretch">
      <div class="col-12">
        <div class="card contrato-card h-100">
          <div class="card-header">
            <div class="card-title">
              <h5 class="mb-0 text-with">Detalhes:</h5>
            </div>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body" id="detalhesContrato">
            <div class="row">
              <div class="col-md-6">
                <dl class="row mb-0">
                  <dt class="col-sm-4">Situação</dt>
                  <dd class="col-sm-8">{{ optional($contrato->situacaoContrato)->nome ?? '—' }}</dd>
                  <dt class="col-sm-4">Fiscais</dt>
                  <dd class="col-sm-8">Téc.: {{ optional($contrato->fiscalTecnico)->nome_completo ?? '—' }} | Adm.: {{ optional($contrato->fiscalAdministrativo)->nome_completo ?? '—' }}</dd>
                  <dt class="col-sm-4">Gestor</dt>
                  <dd class="col-sm-8">{{ optional($contrato->gestor)->nome_completo ?? '—' }}</dd>
                  <dt class="col-sm-4">Número</dt>
                  <dd class="col-sm-8">{{ $contrato->numero ?? '—' }}</dd>
                  <dt class="col-sm-4">Processo Origem</dt>
                  <dd class="col-sm-8">{{ $contrato->processo_origem ?? '—' }}</dd>
                  <dt class="col-sm-4">Modalidade</dt>
                  <dd class="col-sm-8">{{ $contrato->modalidade ?? '—' }}</dd>
                  <dt class="col-sm-4">Data Assinatura</dt>
                  <dd class="col-sm-8">{{ $contrato->data_assinatura ?? '—' }}</dd>
                  <dt class="col-sm-4">Início Vigência</dt>
                  <dd class="col-sm-8">{{ $contrato->data_inicio_vigencia ?? '—' }}</dd>
                  <dt class="col-sm-4">Fim Vigência</dt>
                  <dd class="col-sm-8">{{ $contrato->data_fim_vigencia ?? '—' }}</dd>
                  <dt class="col-sm-4">Valor Global</dt>
                  <dd class="col-sm-8">{{ $contrato->valor_global !== null ? ('R$ ' . number_format((float)$contrato->valor_global, 2, ',', '.')) : '—' }}</dd>
                  <dt class="col-sm-4">Valor Mensal</dt>
                  <dd class="col-sm-8">{{ $contrato->valor_mensal !== null ? ('R$ ' . number_format((float)$contrato->valor_mensal, 2, ',', '.')) : '—' }}</dd>
                  <dt class="col-sm-4">Meses Contratados</dt>
                  <dd class="col-sm-8">{{ $contrato->quantidade_meses ?? '—' }}</dd>
                  <dt class="col-sm-4">Contratada</dt>
                  <dd class="col-sm-8">{{ optional($contrato->contratada)->razao_social ?? ($contrato->empresa_razao_social ?? '—') }}</dd>
                  <dt class="col-sm-4">CNPJ</dt>
                  <dd class="col-sm-8">{{ optional($contrato->contratada)->cnpj ?? ($contrato->empresa_cnpj ?? '—') }}</dd>
                  <dt class="col-sm-4">E-mail</dt>
                  <dd class="col-sm-8">{{ optional($contrato->contratada)->email ?? ($contrato->empresa_email ?? '—') }}</dd>
                  <dt class="col-sm-4">Representante</dt>
                  <dd class="col-sm-8">{{ $contrato->empresa_representante ?? '—' }}</dd>
                  <dt class="col-sm-4">Contato</dt>
                  <dd class="col-sm-8">{{ $contrato->empresa_contato ?? '—' }}</dd>
                  <dt class="col-sm-4">Endereço</dt>
                  <dd class="col-sm-8">{{ $contrato->empresa_endereco ?? '—' }}</dd>
                </dl>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="text-muted small">Objeto</div>
                  <div>{{ $contrato->objeto ?? '—' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-muted small">Local de Prestação do Serviço</div>
                  <div>{{ $contrato->empresa_endereco ?? '—' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-muted small">Vigência</div>
                  <div>{{ $totais['vigencia_periodo'] ?? '—' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 🔹 Vigência do Contrato (dias restantes) -->
    <div class="row g-3 align-items-stretch">
      <div class="col-12">
        <div class="card contrato-card mb-3 h-100">
          <div class="card-header">
            <div class="card-title">
              <h5 class="mb-0 text-with">Vigência:</h5>
            </div>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body" id="cardContadorVigencia">
            @php $tipo = $totais['vigencia_tipo'] ?? null; $texto = $totais['vigencia_texto'] ?? 'Vigência não disponível.'; @endphp
            @if($tipo === 'restante')
              <p class="mb-1">{{ $texto }}</p>
            @elseif($tipo === 'vencido')
              <p class="text-danger mb-0">{{ $texto }}</p>
            @else
              <p class="text-muted mb-0">{{ $texto }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>

     <!-- 🔹 Itens Contratados -->
    <div class="card contrato-card mt-4">
      <div class="card-header">
        <div class="card-title">
          <h5 class="mb-0 text-with">Itens Contratados: </h5>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          <a id="btnInserirItens" href="{{ route('contratos.itens', $id) }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus-circle me-1"></i> Inserir Itens
          </a>
        </div>
      </div><!-- /.card-header -->
      <div class="card-body" id="tabelaItens">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Descrição</th>
              <th>Unid.</th>
              <th class="text-end">Qtd</th>
              <th class="text-end">Meses</th>
              <th class="text-end">Valor Unit.</th>
              <th class="text-end">Valor Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($itens_list ?? []) as $i)
              <tr>
                <td>{{ $i['descricao'] }}</td>
                <td>{{ $i['unidade'] }}</td>
                <td class="text-end">{{ $i['quantidade_br'] }}</td>
                <td class="text-end">{{ $i['meses'] ?? '—' }}</td>
                <td class="text-end">{{ $i['valor_unitario_br'] }}</td>
                <td class="text-end">{{ $i['valor_total_br'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

     <!-- 🔹 Documentos -->
    <div class="card contrato-card mt-4">
      <div class="card-header">
        <div class="card-title">
          <h5 class="mb-0 text-with">Documentos:</h5>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          @can('contratos.anexar_documento')
          <a id="btnNovoDocumento" href="{{ route('contratos.documentos.create', $id) }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-file-upload"></i> Cadastrar Documento
          </a>
          @endcan
        </div>
      </div>
      <div class="card-body" id="tabelaDocumentosRelacionados">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Título</th>
              <th>Tipo</th>
              <th>Upload</th>
              <th>Arquivo</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($documentos_list ?? []) as $d)
              <tr>
                <td>{{ $d['titulo'] }}</td>
                <td>{{ $d['tipo_nome'] }}</td>
                <td>{{ $d['data_upload_br'] }}</td>
                <td>
                  @if(!empty($d['arquivo_url']))
                    <a href="{{ $d['arquivo_url'] }}" target="_blank" rel="noopener">Abrir</a>
                  @else
                    —
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    </div>

     <!-- 🔹 Empenhos Vinculados -->

              <div class="card card">
              <div class="card-header">
                <div class="card-title">
                 <h5 class="mb-0 text-with"> Empenhos Vinculados: </h5>
                     </div>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                     <a id="btnNovoEmpenho" href="{{ route('empenhos.create') }}?contrato_id={{ $id }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Cadastrar Empenho
            </a>
                </div>
        </div><!-- /.card-header -->
     <div class="card-body" id="tabelaEmpenhos">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Número</th>
              <th class="text-end">Valor Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($empenhos_list ?? []) as $e)
              <tr>
                <td>{{ $e['numero'] }}</td>
                <td class="text-end">{{ $e['valor_total_br'] }}</td>
                <td>{{ $e['status'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
   </div>

    <!-- 🔹 Pagamentos -->
    <div class="card card mt-4" id="cardPagamentos">
      <div class="card-header">
        <div class="card-title">
          <h5 class="mb-0 text-with">Pagamentos:</h5>
        </div>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>
      </div><!-- /.card-header -->
      <div class="card-body" id="tabelaPagamentos">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Empenho</th>
              <th>Documento</th>
              <th class="text-end">Valor</th>
              <th>Data</th>
            </tr>
          </thead>
          <tbody>
            @foreach(($pagamentos_list ?? []) as $p)
              <tr>
                <td>{{ $p['empenho_numero'] }}</td>
                <td>{{ $p['documento'] }}</td>
                <td class="text-end">{{ $p['valor_br'] }}</td>
                <td>{{ $p['data_br'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>


<!-- 🔹 Modal Detalhes do Item -->
<div class="modal fade" id="modalDetalhesItem" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-box me-2"></i>Detalhes do Item</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="conteudoItem">
              <p class="text-muted">Carregando...</p>
          </div>
      </div>
  </div>
</div>

<!-- 🔹 Modal Detalhes do Empenho -->
<div class="modal fade" id="modalDetalhesEmpenho" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>Detalhes do Empenho</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="conteudoEmpenho">
              <p class="text-muted">Carregando...</p>
          </div>
      </div>
  </div>
</div>

<!-- 🔹 Modal Cadastrar Pagamento -->
<div class="modal fade" id="modalPagamento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-success text-white">
              <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Cadastrar Pagamento</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <form id="formPagamento" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" id="empenhoId">
                  <div class="mb-3">
                      <label class="form-label">Empenho</label>
                      <input type="text" id="empenhoNumero" class="form-control" readonly>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Valor do Pagamento (R$)</label>
                      <input type="text" name="valor" class="form-control money-br-input" required>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Data do Pagamento</label>
                      <input type="date" name="data_pagamento" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Comprovante (PDF)</label>
                      <input type="file" name="arquivo_comprovante_pdf" class="form-control" accept="application/pdf">
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Observação</label>
                      <textarea class="form-control" name="observacao" rows="2"></textarea>
                  </div>
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-success" id="btnSalvarPagamento">Cadastrar Pagamento</button>
          </div>
</div>
</div>
</div>
</div>
<!-- 🔹 Modal Upload PDF Emitido -->
<div class="modal fade" id="modalUploadEmitido" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title"><i class="fas fa-file-pdf me-2"></i>Upload do PDF Emitido</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formUploadEmitido">
          <input type="hidden" id="upload_empenho_id">
          <input type="hidden" id="upload_mes">
          <input type="hidden" id="upload_ano">
          <div class="mb-3">
            <label class="form-label">Selecione o PDF</label>
            <input type="file" class="form-control" id="upload_pdf" name="pdf" accept="application/pdf" required>
            <small class="text-muted">Documento: Pretensão de Empenho (PDF).</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmUploadEmitido">Enviar PDF</button>
      </div>
    </div>
  </div>
  </div>
@endsection
<!-- 🔹 Modal Detalhes da Vigência -->
<div class="modal fade" id="modalDetalhesVigencia" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-info text-white">
              <h5 class="modal-title"><i class="fas fa-calendar-alt me-2"></i>Detalhes da Vigência</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="conteudoVigencia">
              <p class="text-muted">Carregando...</p>
          </div>
      </div>
  </div>
</div>

<!-- 🔹 Modal Upload Comprovante de Liquidação (Pago) -->
<div class="modal fade" id="modalUploadPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>Upload do Comprovante de Liquidação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formUploadPago">
                    <input type="hidden" id="upload_pago_empenho_id">
                    <div class="mb-3">
                        <label class="form-label">Comprovante (PDF)</label>
                        <input type="file" class="form-control" id="upload_pago_pdf" name="comprovante" accept="application/pdf" required>
                        <div class="small text-secondary mt-1">O status "Pago" será concluído após o upload do comprovante de liquidação.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmUploadPago">Enviar Comprovante</button>
            </div>
        </div>
    </div>
</div>
<!-- 🔹 Modal Solicitação de Empenho -->
<div class="modal fade" id="modalSolicitarEmpenho" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Solicitar Pretensão de Empenho</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formSolicitarEmpenho">
          <input type="hidden" id="sol_empenho_id">
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label">Mês</label>
              <input type="number" min="1" max="12" class="form-control" id="sol_mes" required>
            </div>
            <div class="col-6">
              <label class="form-label">Ano</label>
              <input type="number" min="2000" max="2100" class="form-control" id="sol_ano" required>
            </div>
            <div class="col-12">
              <label class="form-label">Período de Referência</label>
              <input type="text" class="form-control" id="sol_periodo_referencia" placeholder="Ex.: Janeiro/2025">
              <small class="text-muted">Preenchido automaticamente a partir do mês/ano; você pode ajustar.</small>
            </div>
            <div class="col-12">
              <label class="form-label">Observações</label>
              <textarea class="form-control" id="sol_observacoes" rows="2" placeholder="Notas adicionais (opcional)"></textarea>
            </div>
          </div>
          <div class="mt-3">
            <div class="alert alert-secondary">
              <i class="fas fa-info-circle me-2"></i>
              Confirme as informações básicas. O PDF será gerado e anexado ao contrato somente após a aprovação.
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmSolicitacao">Enviar para Aprovação</button>
      </div>
    </div>
  </div>
  </div>
@section( section:'css')
<style>
.tooltip-inner {
  background-color: rgba(0, 0, 0, 0.85);
  font-size: 0.85rem;
}
</style>

@endsection

@section('js')

<script>
document.addEventListener("DOMContentLoaded", function() {
    const idContrato = window.location.pathname.split('/').pop();

    // =========================
    // Utilidades e Modais (inline)
    // =========================
    function formatarValor(valor) {
      return 'R$ ' + parseFloat(valor || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
    }

    function reativarBootstrapComponentes() {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        if (window.bootstrap && bootstrap.Tooltip) {
          new bootstrap.Tooltip(el);
        }
      });
    }

    // Helpers seguros para manipular DOM
    function setText(id, text) {
      const el = document.getElementById(id);
      if (el) el.innerText = text;
    }
    function setHTML(id, html) {
      const el = document.getElementById(id);
      if (el) el.innerHTML = html;
    }

    // Exibe um modal com compatibilidade BS5/BS4
    function showModal(modalEl) {
      try {
        if (window.bootstrap && typeof bootstrap.Modal === 'function') {
          let instance = null;
          if (typeof bootstrap.Modal.getInstance === 'function') {
            instance = bootstrap.Modal.getInstance(modalEl);
          }
          if (!instance) {
            instance = new bootstrap.Modal(modalEl);
          }
          instance.show();
        } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
          $(modalEl).modal('show');
        } else {
          // Fallback simples
          modalEl.classList.add('show');
          modalEl.style.display = 'block';
          modalEl.setAttribute('aria-modal', 'true');
          modalEl.removeAttribute('aria-hidden');
        }
      } catch (e) {
        console.error('Erro ao abrir modal:', e);
      }
    }

    // Fecha um modal com compatibilidade BS5/BS4
    function hideModal(modalEl) {
      try {
        if (window.bootstrap && typeof bootstrap.Modal === 'function') {
          let instance = null;
          if (typeof bootstrap.Modal.getInstance === 'function') {
            instance = bootstrap.Modal.getInstance(modalEl);
          }
          if (!instance) {
            instance = new bootstrap.Modal(modalEl);
          }
          instance.hide();
        } else if (typeof $ !== 'undefined' && typeof $(modalEl).modal === 'function') {
          $(modalEl).modal('hide');
        } else {
          // Fallback simples
          modalEl.classList.remove('show');
          modalEl.style.display = 'none';
          modalEl.setAttribute('aria-hidden', 'true');
          modalEl.removeAttribute('aria-modal');
        }
      } catch (e) {
        console.error('Erro ao fechar modal:', e);
      }
    }

    // Delegação para botões de fechar (compatível com BS4 e BS5)
    document.addEventListener('click', function(evt) {
      const trigger = evt.target.closest('[data-bs-dismiss="modal"], [data-dismiss="modal"]');
      if (!trigger) return;
      const modalEl = trigger.closest('.modal');
      if (modalEl) {
        // Se Bootstrap já estiver gerenciando, não interfere
        if (!(window.bootstrap && typeof bootstrap.Modal === 'function')) {
          evt.preventDefault();
          hideModal(modalEl);
        }
      }
    });

    function normalizarLabel(chave) {
      return (chave || '')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
    }

    function formatarCampo(chave, valor) {
      if (valor === null || valor === undefined || valor === '') return '—';
      const num = Number(valor);
      if (/valor|total|preco|unitario|montante/i.test(chave) && !isNaN(num)) {
        return formatarValor(num);
      }
      if (/data/i.test(chave)) {
        try {
          const d = new Date(valor);
          if (!isNaN(d.getTime())) return d.toLocaleDateString('pt-BR');
        } catch {}
      }
      return String(valor);
    }

    function isForeignKeyKey(k) {
      return /^id$/i.test(k) || /_id$/i.test(k) || /(Id|ID)$/.test(k);
    }

    function isTimestampKey(k) {
      return (
        /^(created|updated|deleted)_?at$/i.test(k) ||
        /^(createdAt|updatedAt|deletedAt)$/i.test(k) ||
        /^timestamps?$/i.test(k)
      );
    }

    function renderTabelaDetalhes(obj, destaque = []) {
      if (!obj || typeof obj !== 'object') return '<p class="text-muted">Sem dados.</p>';

      const keys = Object.keys(obj).filter(k => !isForeignKeyKey(k) && !isTimestampKey(k));

      const isPrimitive = v => (v === null || v === undefined) ? true : (typeof v !== 'object');
      const primaries = keys.filter(k => isPrimitive(obj[k]));
      const orderedPrimaries = [
        ...destaque.filter(k => primaries.includes(k)),
        ...primaries.filter(k => !destaque.includes(k))
      ];

      let linhas = '';
      orderedPrimaries.forEach(k => {
        linhas += `<tr><th>${normalizarLabel(k)}</th><td>${formatarCampo(k, obj[k])}</td></tr>`;
      });

      // Arrays: mostra o tamanho ou lista simples se forem primitivos
      const arrays = keys.filter(k => Array.isArray(obj[k]));
      arrays.forEach(k => {
        const arr = obj[k] || [];
        if (!arr.length) return;
        const allPrimitive = arr.every(v => isPrimitive(v));
        if (allPrimitive) {
          const preview = arr.map(v => formatarCampo(k, v)).join(', ');
          linhas += `<tr><th>${normalizarLabel(k)}</th><td>${preview}</td></tr>`;
        } else {
          linhas += `<tr><th>${normalizarLabel(k)}</th><td>${arr.length} registro(s)</td></tr>`;
        }
      });

      // Objetos relacionados: imprime campos internos (um nível), filtrando FKs
      const nested = keys.filter(k => obj[k] && typeof obj[k] === 'object' && !Array.isArray(obj[k]));
      nested.forEach(k => {
        const child = obj[k];
        const childKeys = Object.keys(child).filter(ck => !isForeignKeyKey(ck) && !isTimestampKey(ck) && isPrimitive(child[ck]));
        if (!childKeys.length) return;
        linhas += `<tr class="table-light"><th colspan="2">${normalizarLabel(k)}</th></tr>`;
        childKeys.forEach(ck => {
          linhas += `<tr><td class="fw-semibold">${normalizarLabel(ck)}</td><td>${formatarCampo(ck, child[ck])}</td></tr>`;
        });
      });

      return `<table class="table table-striped align-middle">${linhas}</table>`;
    }

    window.abrirModalItem = function(item) {
      const modalEl = document.getElementById('modalDetalhesItem');
      const corpo = modalEl.querySelector('#conteudoItem');
      corpo.innerHTML = renderTabelaDetalhes(item, [
        'descricao_item','unidade_medida','quantidade','valor_unitario','valor_total','tipo_item','justificativa'
      ]);
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    window.abrirModalEmpenho = function(emp) {
      const modalEl = document.getElementById('modalDetalhesEmpenho');
      const corpo = modalEl.querySelector('#conteudoEmpenho');
      corpo.innerHTML = renderTabelaDetalhes(emp, [
        'numero','data_empenho','valor','projeto_atividade','fonte_recurso','elemento_despesa','observacao'
      ]);
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    window.abrirModalPagamento = function(empenhoId, numeroEmpenho) {
      const modalEl = document.getElementById('modalPagamento');
      document.getElementById('empenhoId').value = empenhoId;
      document.getElementById('empenhoNumero').value = numeroEmpenho;
      const formEl = document.getElementById('formPagamento');
      if (formEl) {
        formEl.action = `{{ route('financeiro.pagamentos.store', '__ID__') }}`.replace('__ID__', empenhoId);
        formEl.method = 'POST';
        formEl.enctype = 'multipart/form-data';
      }
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    window.abrirModalVigencia = function(info) {
      const modalEl = document.getElementById('modalDetalhesVigencia');
      const corpo = modalEl.querySelector('#conteudoVigencia');
      const obj = {
        tipo: info.tipo || '—',
        numero: info.numero || '—',
        assinatura: info.assinatura || '—',
        periodo_inicio: info.periodo_inicio || '—',
        periodo_fim: info.periodo_fim || '—',
        nova_data_fim: info.nova_data_fim || '—'
      };
      corpo.innerHTML = renderTabelaDetalhes(obj, ['tipo','numero','assinatura','periodo_inicio','periodo_fim','nova_data_fim']);
      reativarBootstrapComponentes();
      showModal(modalEl);
    };

    // Renderização 100% server-side: removido carregamento AJAX de contrato

// Inicializa ou reativa tooltips
function inicializarTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
}

    // Controle de permissão para exibir ações de aprovação
        const podeAprovar = {{ auth()->user() && auth()->user()->hasRole(['Administrador','Gestor de Contrato']) ? 'true' : 'false' }};
        const isFiscalAdm = {{ auth()->user() && auth()->user()->hasRole(['Fiscal','Fiscal de contrato']) ? 'true' : 'false' }};


    // Mantido apenas utilitários visuais e modais





    // Confirma solicitação: envia para aprovação (sem gerar PDF agora)
    const btnConfirmSolicitacao = document.getElementById('btnConfirmSolicitacao');
    if (btnConfirmSolicitacao && !btnConfirmSolicitacao._bound) {
      btnConfirmSolicitacao._bound = true;
      btnConfirmSolicitacao.addEventListener('click', function(){
        const id = document.getElementById('sol_empenho_id').value;
        const mes = document.getElementById('sol_mes').value;
        const ano = document.getElementById('sol_ano').value;
        const periodo_referencia = document.getElementById('sol_periodo_referencia').value;
        const observacoes = document.getElementById('sol_observacoes').value;

        const url = `{{ route('empenhos.solicitacao_salvar', '__ID__') }}`.replace('__ID__', id);
        fetch(url, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify({ mes, ano, periodo_referencia, observacoes })
        }).then(resp => {
          if (!resp.ok) return resp.json().then(j => { throw new Error(j.message || 'Falha ao salvar solicitação'); });
          return resp.json();
        }).then((json) => {
          hideModal(document.getElementById('modalSolicitarEmpenho'));
          // Atualiza status e desabilita botão na linha correspondente
          const tabelaEl = document.getElementById('tabelaEmpenhos');
          const btn = tabelaEl.querySelector(`button.acao-solicitar[data-id="${id}"]`);
          if (btn) {
            btn.disabled = true;
            const tr = btn.closest('tr');
            const statusTd = tr ? tr.querySelector('.status-cell') : null;
            if (statusTd) {
              statusTd.innerHTML = '<span class="badge rounded-pill bg-warning text-dark" title="Solicitado">Solicitado</span>';
            }
            // Se houver botão de aprovar na linha, habilita-o e associa o ID da solicitação
            const btnAprovar = tr ? tr.querySelector('button.acao-aprovar-solicitacao') : null;
            if (btnAprovar && json && json.solicitacao_id) {
              btnAprovar.dataset.solicitacaoId = json.solicitacao_id;
              btnAprovar.disabled = false;
              btnAprovar.title = 'Aprovar solicitação pendente';
            }
          }
          alert('Solicitação registrada e encaminhada para aprovação.');
        }).catch(err => {
          alert('Falha ao salvar solicitação: ' + (err.message || 'erro desconhecido'));
        });
      });
    }

    // ========== 4️⃣ Tabela de Empenhos ==========
    /* Removido preenchimento dinâmico de Empenhos (SSR) */
        const emp = data.empenhos ?? [];
        const destino = document.getElementById('tabelaEmpenhos');
        const mesesNome = ['', 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

        // Exercícios disponíveis
        const anosSet = new Set();
        emp.forEach(e => {
            if (e.data_empenho) {
                const dt = new Date(e.data_empenho);
                if (!isNaN(dt)) anosSet.add(dt.getFullYear());
            }
        });
        const anoAtual = new Date().getFullYear();
        anosSet.add(anoAtual);
        const anos = Array.from(anosSet).sort((a,b) => b - a);
        const selectedAno = window.__selectedExercicio || anos[0] || anoAtual;
        window.__selectedExercicio = selectedAno;

        // Monta linhas por mês
        let linhas = '';
        const agora = new Date();
        const anoAtualGlobal = agora.getFullYear();
        const mesAtualGlobal = agora.getMonth() + 1;
        const diaHoje = agora.getDate();
        const ultimoDiaMesAtual = new Date(anoAtualGlobal, mesAtualGlobal, 0).getDate();
        const ultimoDiaMesCorrente = diaHoje >= ultimoDiaMesAtual; // habilita a partir do último dia
        for (let m = 1; m <= 12; m++) {
            const lista = emp.filter(e => {
                if (!e.data_empenho) return false;
                const dt = new Date(e.data_empenho);
                return dt.getFullYear() === selectedAno && (dt.getMonth() + 1) === m;
            });
            const e = lista.length ? lista[lista.length - 1] : null; // último do mês, se houver
            const dataEmp = e?.data_empenho ? new Date(e.data_empenho).toLocaleDateString('pt-BR') : '—';
            const valor = e?.valor ?? 0;
            const mesFinalizado = (selectedAno < anoAtualGlobal) || (selectedAno === anoAtualGlobal && m < mesAtualGlobal);
            const mesCorrente = (selectedAno === anoAtualGlobal && m === mesAtualGlobal);
            const solicitacoes = Array.isArray(e?.solicitacoes) ? e.solicitacoes : [];
            const pendenteMes = e ? solicitacoes.find(s => s.status === 'pendente' && Number(s.mes) === Number(m) && Number(s.ano) === Number(selectedAno)) : null;
            const aprovadoMes = e ? solicitacoes.find(s => s.status === 'aprovado' && Number(s.mes) === Number(m) && Number(s.ano) === Number(selectedAno)) : null;

            // Determina status consolidado
            let statusBadge = '';
            const fmtData = (iso) => {
                if (!iso) return '';
                const d = new Date(iso);
                if (isNaN(d)) return '';
                return d.toLocaleDateString('pt-BR');
            };
            const pdfLinkHtml = aprovadoMes?.pdf_path ? ` <a href="/storage/${aprovadoMes.pdf_path}" target="_blank" rel="noopener" class="ms-1" title="Abrir PDF da pretensão"><i class="fas fa-file-pdf"></i></a>` : '';
            if (pendenteMes) {
                const quem = pendenteMes.solicitante?.name || '—';
                const quando = fmtData(pendenteMes.solicitado_at);
                statusBadge = `<span class="badge rounded-pill bg-warning text-dark" title="Solicitado por ${quem}${quando?` em ${quando}`:''}">Solicitado</span>`;
            } else if (aprovadoMes && !e?.emitido_at) {
                const quem = aprovadoMes.aprovador?.name || '—';
                const quando = fmtData(aprovadoMes.aprovado_at);
                statusBadge = `<span class="badge rounded-pill bg-info text-dark" title="Aprovado por ${quem}${quando?` em ${quando}`:''}">Aguardando emissão</span>${pdfLinkHtml}`;
            } else if (e?.emitido_at && !e?.pago_at) {
                statusBadge = '<span class="badge rounded-pill bg-primary" title="Aguardando pagamento">Aguardando pagamento</span>';
            } else if (e?.pago_at) {
                statusBadge = '<span class="badge rounded-pill bg-success" title="Pago">Pago</span>';
            } else if (mesCorrente) {
                statusBadge = '<span class="badge rounded-pill bg-secondary" title="Mês corrente em andamento">Em curso</span>';
            } else if (!e) {
                statusBadge = '<span class="badge rounded-pill bg-light text-muted" title="Sem empenho">Sem empenho</span>';
            } else {
                statusBadge = '<span class="badge rounded-pill bg-secondary" title="—">—</span>';
            }

            linhas += `
                <tr>
                    <td>${m}</td>
                    <td>${mesesNome[m]}</td>
                    <td>${e?.numero ?? '—'}</td>
                    <td>${dataEmp}</td>
                    <td data-format="currency" data-value="${valor}"></td>
                    <td class="status-cell">${statusBadge}</td>
                    <td>
                        ${e ? `<button class="btn btn-outline-primary btn-sm ver-empenho" data-empenho='${JSON.stringify(e).replace(/'/g,"&apos;")}'>
                            <i class="fas fa-search"></i>
                        </button>` : `<button class="btn btn-outline-secondary btn-sm" disabled title="Sem empenho">
                            <i class="fas fa-search"></i>
                        </button>`}
                        ${e ? (() => {
                            const podePagar = !!e?.emitido_at;
                            const reasonPagar = podePagar ? 'Registrar pagamento' : 'Emissão não registrada';
                            return `<button class="btn btn-outline-success btn-sm pagar-empenho" data-empenho-id="${e.id}" data-empenho-numero="${e.numero}" ${podePagar ? '' : 'disabled'} title="${reasonPagar}">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>`;
                        })() : `<button class="btn btn-outline-success btn-sm" disabled title="Sem empenho">
                            <i class="fas fa-money-bill-wave"></i>
                        </button>`}
                        ${e ? (() => {
                            const podeSolicitar = (!pendenteMes && !aprovadoMes && !e?.emitido_at && !e?.pago_at && (mesFinalizado || (isFiscalAdm && mesCorrente && ultimoDiaMesCorrente)));
                            const reason = !e ? 'Sem empenho'
                                : pendenteMes ? 'Já existe solicitação pendente'
                                : aprovadoMes ? 'Já existe solicitação aprovada'
                                : e?.emitido_at ? 'Já emitido'
                                : e?.pago_at ? 'Já pago'
                                : (mesCorrente && isFiscalAdm && !ultimoDiaMesCorrente) ? 'Disponível a partir do último dia do mês'
                                : (!mesFinalizado && !(isFiscalAdm && mesCorrente && ultimoDiaMesCorrente)) ? 'Mês não elegível'
                                : 'Solicitar pretensão';
                            return `<button class="btn btn-outline-primary btn-sm ms-1 acao-solicitar" data-id="${e.id}" data-mes="${m}" data-ano="${selectedAno}" ${podeSolicitar ? '' : 'disabled'} title="${reason}">
                                <i class="fas fa-paper-plane"></i>
                            </button>`; })() : `<button class="btn btn-outline-primary btn-sm ms-1" disabled title="Sem empenho">
                                <i class="fas fa-paper-plane"></i>
                            </button>`}
                        ${podeAprovar ? (() => {
                            const temPendencia = !!e?.solicitacao_pendente_id;
                            const reasonAprovar = temPendencia ? 'Aprovar solicitação' : 'Sem solicitação pendente';
                            return `<button class="btn btn-outline-primary btn-sm ms-1 acao-aprovar-solicitacao" data-solicitacao-id="${e?.solicitacao_pendente_id ?? ''}" ${temPendencia ? '' : 'disabled'} title="${reasonAprovar}">
                                <i class="fas fa-check-circle"></i>
                            </button>`;
                        })() : ''}
                        ${e ? (() => {
                            const podeUploadPago = !!e?.emitido_at && !e?.pago_at;
                            const reasonUploadPago = e?.pago_at ? 'Já pago' : (!e?.emitido_at ? 'Emissão não registrada' : 'Enviar comprovante de liquidação');
                            return `<button class="btn btn-outline-success btn-sm ms-1 acao-upload-pago" data-id="${e.id}" ${podeUploadPago ? '' : 'disabled'} title="${reasonUploadPago}">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </button>`;
                        })() : `<button class="btn btn-outline-success btn-sm ms-1" disabled title="Sem empenho">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </button>`}
                    </td>
                </tr>`;
        }

        const seletorAno = `
            <div class="d-flex justify-content-end mb-2">
                <label class="me-2 fw-bold">Exercício:</label>
                <select id="filtroExercicio" class="form-select form-select-sm" style="max-width:140px;">
                    ${anos.map(a => `<option value="${a}" ${a===selectedAno?'selected':''}>${a}</option>`).join('')}
                </select>
            </div>`;

        destino.innerHTML = `
            ${seletorAno}
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr><th>Mês</th><th>Nome</th><th>Número</th><th>Data</th><th>Valor (R$)</th><th>Status</th><th>Ações</th></tr>
                </thead><tbody>${linhas}</tbody>
            </table>`;

        // Listener do filtro de exercício
        const sel = document.getElementById('filtroExercicio');
        if (sel) {
            sel.addEventListener('change', (ev) => {
                window.__selectedExercicio = parseInt(ev.target.value, 10);
                preencherEmpenhos(data);
            });
        }

        // Bind botões
        document.querySelectorAll('.ver-empenho').forEach(btn => {
            btn.addEventListener('click', e => {
                const empSel = JSON.parse(e.currentTarget.getAttribute('data-empenho'));
                abrirModalEmpenho(empSel);
            });
        });
        document.querySelectorAll('.pagar-empenho').forEach(btn => {
            btn.addEventListener('click', e => {
                const id = e.currentTarget.dataset.empenhoId;
                const numero = e.currentTarget.dataset.empenhoNumero;
                abrirModalPagamento(id, numero);
            });
        });

        // Delegação para ações de solicitação, aprovação e upload do comprovante
        const tabelaEl = document.getElementById('tabelaEmpenhos');
        let pagoTargetBadge = null;
        let pagoTargetButton = null;
        tabelaEl.addEventListener('click', function(evt){
            const btnSolicitar = evt.target.closest('button.acao-solicitar');
            const btnAprovar = evt.target.closest('button.acao-aprovar-solicitacao');
            const btnUploadPago = evt.target.closest('button.acao-upload-pago');
            if (btnSolicitar) {
                evt.preventDefault();
                if (btnSolicitar.disabled) return;
                const id = btnSolicitar.getAttribute('data-id');
                const mes = parseInt(btnSolicitar.getAttribute('data-mes'), 10);
                const ano = parseInt(btnSolicitar.getAttribute('data-ano'), 10);
                const nomeMes = (n) => mesesNome[n] || '';
                document.getElementById('sol_empenho_id').value = id;
                document.getElementById('sol_mes').value = mes;
                document.getElementById('sol_ano').value = ano;
                document.getElementById('sol_periodo_referencia').value = `${nomeMes(mes)}/${ano}`;
                document.getElementById('sol_observacoes').value = '';
                reativarBootstrapComponentes();
                showModal(document.getElementById('modalSolicitarEmpenho'));
            }
            if (btnAprovar) {
                evt.preventDefault();
                if (btnAprovar.disabled) return;
                const solicitacaoId = btnAprovar.getAttribute('data-solicitacao-id');
                if (!solicitacaoId) {
                    alert('Nenhuma solicitação pendente associada a este empenho.');
                    return;
                }
                const urlApr = `{{ route('empenhos.solicitacoes.aprovar', '__ID__') }}`.replace('__ID__', solicitacaoId);
                fetch(urlApr, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                }).then(resp => {
                    if (!resp.ok) return resp.json().then(j => { throw new Error(j.message || 'Falha ao aprovar solicitação'); });
                    const ct = resp.headers.get('content-type') || '';
                    btnAprovar.__lastResponseContentType = ct;
                    return resp.json().then(data => { btnAprovar.__lastResponseData = data; return data; });
                }).then((data) => {
                    const tr = btnAprovar.closest('tr');
                    const statusTd = tr ? tr.querySelector('.status-cell') : null;
                    if (statusTd) {
                        const link = data?.pdf_path ? ` <a href="/storage/${data.pdf_path}" target="_blank" rel="noopener" class="ms-1" title="Abrir PDF da pretensão"><i class="fas fa-file-pdf"></i></a>` : '';
                        statusTd.innerHTML = '<span class="badge rounded-pill bg-info text-dark" title="Aguardando emissão">Aguardando emissão</span>' + link;
                    }
                    btnAprovar.disabled = true;
                    btnAprovar.title = 'Solicitação aprovada';
                    alert('Solicitação aprovada. PDF gerado e anexado aos Documentos do contrato.');
                }).catch(err => {
                    alert('Falha na aprovação da solicitação: ' + (err.message || 'erro desconhecido'));
                });
            }
            if (btnUploadPago) {
                evt.preventDefault();
                if (btnUploadPago.disabled) return;
                const id = btnUploadPago.getAttribute('data-id');
                pagoTargetBadge = btnUploadPago.closest('tr')?.querySelector('a.badge[data-stage="pago"]') || null;
                pagoTargetButton = btnUploadPago;
                document.getElementById('upload_pago_empenho_id').value = id;
                reativarBootstrapComponentes();
                showModal(document.getElementById('modalUploadPago'));
            }
        });

        // Upload emitido
        const btnUpload = document.getElementById('btnConfirmUploadEmitido');
        if (btnUpload && !btnUpload._bound) {
            btnUpload._bound = true;
            btnUpload.addEventListener('click', function(){
                const id = document.getElementById('upload_empenho_id').value;
                const formEl = document.getElementById('formUploadEmitido');
                const fd = new FormData(formEl);
                const url = `{{ route('empenhos.emitido_upload', '__ID__') }}`.replace('__ID__', id);
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: fd
                }).then(resp => {
                    if (!resp.ok) return resp.json().then(j => { throw new Error(j.message || 'Falha no upload'); });
                    return resp.json();
                }).then(() => {
                    hideModal(document.getElementById('modalUploadEmitido'));
                    // Atualiza célula de status para "Aguardando pagamento"
                    const tabelaEl = document.getElementById('tabelaEmpenhos');
                    const linhas = tabelaEl.querySelectorAll('tbody tr');
                    linhas.forEach(tr => {
                        const statusTd = tr.querySelector('.status-cell');
                        const badge = statusTd?.querySelector('.badge');
                        if (badge && badge.textContent.trim() === 'Aguardando emissão') {
                            statusTd.innerHTML = '<span class="badge rounded-pill bg-primary" title="Aguardando pagamento">Aguardando pagamento</span>';
                        }
                    });
                    if (emitidoTargetButton) {
                        emitidoTargetButton.disabled = true;
                    }
                    alert('PDF enviado com sucesso. Etapa Emitido concluída.');
                }).catch(err => {
                    alert('Falha no upload do PDF: ' + (err.message || 'erro desconhecido'));
                });
            });
        }

        // Upload comprovante (Pago)
        const btnUploadPago = document.getElementById('btnConfirmUploadPago');
        if (btnUploadPago && !btnUploadPago._bound) {
            btnUploadPago._bound = true;
            btnUploadPago.addEventListener('click', function(){
                const id = document.getElementById('upload_pago_empenho_id').value;
                const formEl = document.getElementById('formUploadPago');
                const fd = new FormData(formEl);
                const url = `{{ route('empenhos.pago_upload', '__ID__') }}`.replace('__ID__', id);
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: fd
                }).then(resp => {
                    if (!resp.ok) return resp.json().then(j => { throw new Error(j.message || 'Falha no upload'); });
                    return resp.json();
                }).then(() => {
                    hideModal(document.getElementById('modalUploadPago'));
                    const tr = pagoTargetButton ? pagoTargetButton.closest('tr') : null;
                    const statusTd = tr ? tr.querySelector('.status-cell') : null;
                    if (statusTd) {
                        statusTd.innerHTML = '<span class="badge rounded-pill bg-success" title="Pago">Pago</span>';
                    }
                    if (pagoTargetButton) {
                        pagoTargetButton.disabled = true;
                    }
                    alert('Comprovante enviado com sucesso. Etapa Pago concluída.');
                    document.getElementById('upload_pago_pdf').value = '';
                }).catch(err => {
                    alert('Falha no upload do comprovante: ' + (err.message || 'erro desconhecido'));
                });
            });
        }
    }

    // ========== 4️⃣b Tabela de Pagamentos ==========
    /* Removido preenchimento dinâmico de Pagamentos (SSR) */
        const destino = document.getElementById('tabelaPagamentos');
        if (!destino) return;

        const contratoId = data.id ?? {{ $id }};
        const url = `/api/pagamentos?contrato_id=${contratoId}`;

        fetch(url)
            .then(resp => {
                const ct = resp.headers.get('content-type') || '';
                if (!resp.ok) {
                    return resp.text().then(txt => { throw new Error(`HTTP ${resp.status}: ${txt.slice(0,200)}`); });
                }
                if (ct.includes('application/json')) return resp.json();
                return resp.text().then(() => { throw new Error('Resposta inesperada do servidor para pagamentos.'); });
            })
            .then(lista => {
                const pagamentos = Array.isArray(lista) ? lista : (lista?.data || []);
                if (!pagamentos.length) {
                    destino.innerHTML = `<p class="text-muted mb-0">Nenhum pagamento registrado para este contrato.</p>`;
                    return;
                }

                let linhas = '';
                pagamentos.forEach((p, i) => {
                    const dataPag = p.data_pagamento ? new Date(p.data_pagamento).toLocaleDateString('pt-BR') : '—';
                    const valor = (p.valor_pagamento ?? p.valor ?? 0);
                    const numeroEmp = p.empenho?.numero ?? p.empenho_numero ?? '—';
                    const doc = p.documento ?? '—';
                    const obs = p.observacao ?? '—';
                    linhas += `
                        <tr>
                            <td>${i+1}</td>
                            <td>${numeroEmp}</td>
                            <td>${dataPag}</td>
                            <td>R$ ${parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</td>
                            <td class="text-truncate" style="max-width:220px">${doc}</td>
                            <td class="text-truncate" style="max-width:300px">${obs}</td>
                        </tr>`;
                });

                destino.innerHTML = `
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Empenho</th>
                                <th>Data</th>
                                <th>Valor (R$)</th>
                                <th>Documento</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>${linhas}</tbody>
                    </table>`;
            })
            .catch(err => {
                console.error('Erro ao carregar pagamentos:', err);
                destino.innerHTML = `<div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>Não foi possível carregar os pagamentos do contrato.</div>
                </div>`;
            });
    }

    // ========== 5️⃣ Salvar Pagamento (SSR via formulário) ==========
    document.getElementById('btnSalvarPagamento').addEventListener('click',()=>{
        const formEl = document.getElementById('formPagamento');
        if (!formEl || !formEl.action) return;
        formEl.submit();
    });

    // Atualiza visual dos cards com os valores SSR ao abrir
    atualizarCardDinamico('resumoGlobal', {{ (float) ($totais['valor_global'] ?? 0) }});
    atualizarCardDinamico('resumoEmpenhado', {{ (float) ($totais['valor_empenhado'] ?? 0) }});
    atualizarCardDinamico('resumoPago', {{ (float) ($totais['valor_pago'] ?? 0) }});
    atualizarCardDinamico('resumoSaldo', {{ (float) ($totais['saldo'] ?? 0) }});
function atualizarCardDinamico(id, valor) {
    const elemento = document.getElementById(id);
    const box = elemento.closest('.small-box');
    const iconDiv = box.querySelector('.icon'); // onde o ícone será trocado

    const valorNumerico = parseFloat(valor || 0);
    elemento.innerText = 'R$ ' + valorNumerico.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

    // Remove classes antigas
    box.classList.remove('bg-success', 'bg-warning', 'bg-danger');

    // Define cor, tooltip e ícone conforme a faixa de valor
    let cor = 'bg-success';
    let tooltip = '🟢 Valor saudável — contrato com boa margem financeira';
    let icone = '<i class="fas fa-check-circle"></i>'; // ícone verde padrão

    if (valorNumerico <= 1000) {
        cor = 'bg-danger';
        tooltip = '🔴 Valor crítico — saldo muito baixo ou contrato com risco';
        icone = '<i class="fas fa-exclamation-triangle"></i>'; // alerta vermelho
    } else if (valorNumerico <= 50000) {
        cor = 'bg-warning';
        tooltip = '🟡 Valor moderado — atenção ao acompanhamento dos gastos';
        icone = '<i class="fas fa-exclamation-circle"></i>'; // atenção amarela
    }

    // Atualiza cor e tooltip
    box.classList.add(cor);
    box.setAttribute('data-bs-original-title', tooltip);

    // Atualiza ícone visual
    iconDiv.innerHTML = icone;
}
  //formatar data
  function formatarDataBR(dataISO) {
  if (!dataISO) return '—';
  try {
    const data = new Date(dataISO);
    if (isNaN(data)) return '—'; // evita erro se formato inválido
    return data.toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  } catch {
    return '—';
  }
}

    // Removida a listagem separada de termos aditivos; eles aparecem em Documentos Relacionados

    // ========== 6️⃣ Documentos Relacionados ==========
    /* Removido preenchimento dinâmico de Documentos (SSR) */
        const el = document.getElementById('tabelaDocumentosRelacionados');
        if (!el) return;
        const allDocs = (data.documentos ?? []).slice();
        if (!allDocs.length) {
            el.innerHTML = `<p class="text-muted mb-0">Nenhum documento relacionado a este contrato.</p>`;
            return;
        }

        const isAditivo = (d) => {
            const tipoStr = String(d.tipo || '').toLowerCase();
            const nomeStr = String(d.documento_tipo?.nome || '').toLowerCase();
            return tipoStr === 'termo_aditivo' || nomeStr.includes('aditivo');
        };

        const sortDocs = (docs) => {
            return docs.slice().sort((a,b) => {
                const rankA = isAditivo(a) ? 0 : 1;
                const rankB = isAditivo(b) ? 0 : 1;
                if (rankA !== rankB) return rankA - rankB;
                const da = a.data_upload ? new Date(a.data_upload) : null;
                const db = b.data_upload ? new Date(b.data_upload) : null;
                if (da && db) return db - da; // mais recente primeiro
                return 0;
            });
        };

        const renderTable = (docs) => {
            let linhas = '';
            docs.forEach(d => {
                const tipoAmigavel = (d.documento_tipo && d.documento_tipo.nome) ? d.documento_tipo.nome : (d.tipo || '—');
                const titulo = d.titulo || '—';
                const dataUpload = d.data_upload ? new Date(d.data_upload).toLocaleDateString('pt-BR') : '—';
                const visualizarUrl = `{{ route('documentos.visualizar', '__ID__') }}`.replace('__ID__', d.id) + `?return_to=${encodeURIComponent(window.location.pathname)}`;
                const tipoCell = isAditivo(d)
                    ? `${tipoAmigavel} <span class="badge bg-warning ms-1">Termo Aditivo</span>`
                    : `${tipoAmigavel}`;
                linhas += `<tr>
                    <td>${tipoCell}</td>
                    <td class="text-truncate" style="max-width:260px">${titulo}</td>
                    <td>${dataUpload}</td>
                    <td>
                        <a href="${visualizarUrl}" class="btn btn-outline-primary btn-sm" title="Visualizar documento">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>`;
            });
            return `
                <div class="table-responsive">
                  <table class="table table-striped table-sm align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Tipo</th>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Ações</th>
                      </tr>
                    </thead>
                    <tbody>${linhas}</tbody>
                  </table>
                </div>`;
        };

        const controlsHtml = `
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
              <label class="form-label me-2 mb-0">Filtrar:</label>
              <select class="form-select form-select-sm w-auto d-inline-block" id="filtroDocsTipo">
                <option value="todos">Todos</option>
                <option value="aditivos">Termos Aditivos</option>
                <option value="outros">Outros</option>
              </select>
            </div>
            <small class="text-muted">Ordenado com termos aditivos no topo</small>
          </div>`;

        el.innerHTML = controlsHtml + renderTable(sortDocs(allDocs));

        const select = el.querySelector('#filtroDocsTipo');
        select.addEventListener('change', () => {
            let filtered = allDocs;
            const v = select.value;
            if (v === 'aditivos') filtered = allDocs.filter(isAditivo);
            else if (v === 'outros') filtered = allDocs.filter(d => !isAditivo(d));
            // substitui apenas a tabela mantendo os controles
            const tableContainer = el.querySelector('.table-responsive');
            tableContainer.outerHTML = renderTable(sortDocs(filtered));
        });
    }

    // ========== 7️⃣ Nova Vigência calculada ==========
    /* Removido cálculo dinâmico de vigência (SSR) */
        return;
        const baseFim = data.data_final ? new Date(data.data_final) : null;
        const aditivosPrazo = (data.documentos ?? []).filter(d => d.tipo === 'termo_aditivo' && d.nova_data_fim);
        let maxFim = baseFim;
        aditivosPrazo.forEach(d => {
            try {
                const nd = new Date(d.nova_data_fim);
                if (nd && !isNaN(nd) && (!maxFim || nd > maxFim)) {
                    maxFim = nd;
                }
            } catch {}
        });
        const alertEl = document.getElementById('alertNovaVigencia');
        if (maxFim && (!baseFim || maxFim > baseFim)) {
            alertEl.innerHTML = `
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-calendar-check me-2"></i>
                <div>
                  Nova vigência calculada até <strong>${maxFim.toLocaleDateString('pt-BR')}</strong> com base em termos aditivos.
                </div>
              </div>`;
        } else {
            alertEl.innerHTML = '';
        }
    }

    // ========== 8️⃣ Contador de dias para fim da vigência ==========
    /* Removido contador dinâmico de vigência (SSR) */
        return;
        const baseFim = data.data_final ? new Date(data.data_final) : null;
        const aditivosPrazo = (data.documentos ?? []).filter(d => d.tipo === 'termo_aditivo' && d.nova_data_fim);
        let fim = baseFim;
        aditivosPrazo.forEach(d => {
            try {
                const nd = new Date(d.nova_data_fim);
                if (nd && !isNaN(nd) && (!fim || nd > fim)) {
                    fim = nd;
                }
            } catch {}
        });
        if (!fim || isNaN(fim)) return;

        const agora = new Date();
        const diffMs = fim - agora;
        const diffDias = Math.max(0, Math.ceil(diffMs / (1000 * 60 * 60 * 24)));
        const mesesAprox = diffDias / 30.4375; // média dias/mês

        let badgeClass = 'bg-danger'; // vermelho padrão
        if (mesesAprox > 8) badgeClass = 'bg-primary'; // azul
        else if (mesesAprox > 5) badgeClass = 'bg-success'; // verde
        else if (mesesAprox > 4) badgeClass = 'bg-warning'; // amarelo
        else badgeClass = 'bg-danger'; // vermelho

        const container = document.getElementById('cardContadorVigencia');
        if (!container) return;
        const badgeHtml = `
            <div id="contadorVigencia" class="mb-3">
                <span class="badge ${badgeClass} p-2" style="font-size:1.15rem;">
                    ${diffDias} dias até o final da vigência
                </span>
            </div>`;

        const historicoHtml = renderHistoricoVigencia(data, fim);
        const botaoHtml = renderBotaoAditivoPrazo(data, fim, badgeClass);
        container.innerHTML = badgeHtml + historicoHtml + botaoHtml;
        // Ativa handlers dos botões "Ver detalhes" do histórico
        container.querySelectorAll('.ver-vigencia').forEach(btn => {
            btn.addEventListener('click', e => {
                try {
                    const dataStr = e.currentTarget.getAttribute('data-vigencia');
                    const obj = JSON.parse(dataStr);
                    abrirModalVigencia(obj);
                } catch (err) {
                    console.error('Falha ao abrir detalhes da vigência:', err);
                }
            });
        });
    }

    // Histórico de vigência e termos aditivos
    function renderHistoricoVigencia(data, fimCalculado) {
        const origInicio = data.data_inicio ? new Date(data.data_inicio) : null;
        const origFim = data.data_final ? new Date(data.data_final) : null;
        const aditivos = (data.documentos ?? [])
            .filter(d => d.tipo === 'termo_aditivo' && (d.nova_data_fim || d.data_assinatura))
            .sort((a,b) => new Date(a.data_assinatura || a.nova_data_fim || 0) - new Date(b.data_assinatura || b.nova_data_fim || 0));

        let linhas = '';
        if (origInicio || origFim) {
            const pIni = origInicio ? origInicio.toLocaleDateString('pt-BR') : '—';
            const pFim = origFim ? origFim.toLocaleDateString('pt-BR') : '—';
            const objeto = { tipo: 'Contrato original', periodo_inicio: pIni, periodo_fim: pFim, assinatura: '—' };
            linhas += `<tr>
                <td><span class=\"badge bg-secondary\">Contrato original</span></td>
                <td>${pIni} a ${pFim}</td>
                <td>—</td>
                <td>
                  <button type=\"button\" class=\"btn btn-outline-primary btn-sm ver-vigencia\" data-vigencia='${JSON.stringify(objeto).replace(/'/g, "&apos;")}'>
                    <i class=\"fas fa-eye\"></i>
                  </button>
                </td>
            </tr>`;
        }

        let prevFim = origFim;
        aditivos.forEach(d => {
            const numero = d.numero || d.numero_termo || d.sequencia || '—';
            const assinatura = d.data_assinatura ? new Date(d.data_assinatura).toLocaleDateString('pt-BR') : '—';
            const novoFim = d.nova_data_fim ? new Date(d.nova_data_fim) : null;
            const periodoIni = prevFim ? prevFim.toLocaleDateString('pt-BR') : (origInicio ? origInicio.toLocaleDateString('pt-BR') : '—');
            const periodoFim = novoFim ? novoFim.toLocaleDateString('pt-BR') : '—';
            const objeto = { tipo: 'Aditivo de prazo', numero: numero, assinatura: assinatura, periodo_inicio: periodoIni, periodo_fim: periodoFim, nova_data_fim: d.nova_data_fim || '—' };
            linhas += `<tr>
                <td>Aditivo nº ${numero}</td>
                <td>${periodoIni} a ${periodoFim}</td>
                <td>${assinatura}</td>
                <td>
                  <button type=\"button\" class=\"btn btn-outline-primary btn-sm ver-vigencia\" data-vigencia='${JSON.stringify(objeto).replace(/'/g, "&apos;")}'>
                    <i class=\"fas fa-eye\"></i>
                  </button>
                </td>
            </tr>`;
            if (novoFim) prevFim = novoFim;
        });

        if (!linhas) {
            linhas = `<tr><td colspan=\"4\" class=\"text-muted\">Sem termos aditivos de vigência cadastrados.</td></tr>`;
        }

        return `
        <div id="historicoVigencia">
            <h6 class="mb-2">Histórico de vigência</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Evento</th>
                            <th>Período</th>
                            <th>Assinatura</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>${linhas}</tbody>
                </table>
            </div>
        </div>`;
    }

    function renderBotaoAditivoPrazo(data, fimCalculado, badgeClass) {
        const inicio = data.data_inicio ? new Date(data.data_inicio) : null;
        if (!inicio || !fimCalculado) return '';

        const mesesTotal = Math.max(0, Math.round((fimCalculado - inicio) / (1000 * 60 * 60 * 24 * 30.4375)));
        const isServico = /serv/i.test(String(data.modalidade || '') + ' ' + String(data.objeto || ''));
        const limiteMeses = isServico ? 120 : 60; // 10 anos para serviços (Lei 14.133), 60 meses demais (Lei 8.666)

        const risco = badgeClass === 'bg-warning' || badgeClass === 'bg-danger';
        const podeProrrogar = mesesTotal < limiteMeses;
        if (!risco || !podeProrrogar) return '';

        const url = `{{ route('contratos.documentos.create', $id) }}` + `?tipo=termo_aditivo`;
        return `
        <div class="mt-2">
            <a href="${url}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-calendar-plus me-1"></i> Solicitar aditivo de prazo
            </a>
            <span class="text-muted ms-2">Limite: ${limiteMeses} meses${isServico ? ' (serviços)' : ''}</span>
        </div>`;
    }

});
</script>
@endsection
