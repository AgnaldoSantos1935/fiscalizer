@extends('layouts.app')
@section('title', 'Hosts Cadastrados')

@section('content')
<!-- 🔍 Card de Filtros -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
  <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
      <h4 class="mb-0 text-secondary fw-semibold">
          <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
      </h4>
  </div>

  <div class="card-body bg-white">
      <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">

          <div class="col-md-3">
              <label for="filtroConexao" class="form-label fw-semibold small text-secondary">Nome da Conexão</label>
              <input type="text" id="filtroConexao" class="form-control form-control-sm" placeholder="Ex: VDSDPA0001">
          </div>

          <div class="col-md-3">
              <label for="filtroProvedor" class="form-label fw-semibold small text-secondary">Provedor</label>
              <input list="listaProvedores" id="filtroProvedor" class="form-control form-control-sm" placeholder="Ex: Starlink, Vivo...">
              <datalist id="listaProvedores"></datalist>
          </div>

          <div class="col-md-3">
              <label for="filtroTecnologia" class="form-label fw-semibold small text-secondary">Tecnologia</label>
              <select id="filtroTecnologia" class="form-select form-select-sm">
                  <option value="">Todas</option>
                  <option value="Fibra">Fibra</option>
                  <option value="Rádio">Rádio</option>
                  <option value="Satélite">Satélite</option>
                  <option value="4G">4G</option>
              </select>
          </div>

          <div class="col-md-3">
              <label for="filtroStatus" class="form-label fw-semibold small text-secondary">Status</label>
              <select id="filtroStatus" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <option value="ativo">Ativo</option>
                  <option value="inativo">Inativo</option>
                  <option value="manutenção">Manutenção</option>
              </select>
          </div>

          <div class="col-md-3">
              <label for="filtroMunicipio" class="form-label fw-semibold small text-secondary">Município</label>
              <input list="listaMunicipios" id="filtroMunicipio" class="form-control form-control-sm" placeholder="Ex: Belém, Ananindeua...">
              <datalist id="listaMunicipios"></datalist>
          </div>

          <div class="col-md-3 d-flex justify-content-end align-items-end">
              <div class="d-flex w-100">
                  <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm flex-grow-1 me-2">
                      <i class="fas fa-filter me-1"></i> Filtrar
                  </button>
                  <button type="button" id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm flex-grow-1">
                      <i class="fas fa-undo me-1"></i> Limpar
                  </button>
              </div>
          </div>
      </form>
  </div>
</div>


  <!-- 🔹 Card Principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-file-contract me-2 text-primary"></i>Conexões cadastradas
            </h4>
        </div>

        <div class="card-body bg-white">
              <!-- 🔹 Navbar de ações -->
             <nav class="nav nav-pills flex-column flex-sm-row">

    <ul class="nav nav-pills">
      <li class="nav-item">
        <a id="navDetalhes" class="nav-link inative" aria-current="page" href="#">
          <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('hosts.create') }}" class="nav-link active" aria-current="page">
          <i class="fas fa-plus-circle me-1"></i> Nova Conexão
        </a>
      </li>
    </ul>

</nav>

    <div class="card-body">

        <table id="tabela-hosts" class="table table-striped no-inner-borders w-100">
        </table>
</div>
<!-- 🔹 Modal Detalhes da Conexão -->
<div class="modal fade" id="modalDetalhesConexao" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header bg-primary text-white">
              <h5 class="modal-title"><i class="fas fa-satellite-dish me-2"></i>Detalhes da Conexão</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <p class="text-muted">Carregando...</p>
          </div>
      </div>
  </div>
</div>

    </div>
  </div>
</div>
@endsection
@section('css')
<style>
#formFiltros .btn + .btn { margin-left: .5rem; }
#formFiltros label { font-weight: 600; color: #6c757d; }
#formFiltros input, #formFiltros select { font-size: 0.9rem; }
</style>

@endsection
@section('js')
<script>
$(document).ready(function () {
  const tabela = $('#tabela-hosts').DataTable({
      processing: false,
      serverSide: true,
      ajax: {
          url: '{{ route('api.hosts') }}',
          data: function (d) {
              d.nome_conexao = $('#filtroConexao').val();
              d.provedor     = $('#filtroProvedor').val();
              d.tecnologia   = $('#filtroTecnologia').val();
              d.status       = $('#filtroStatus').val();
              d.municipio    = $('#filtroMunicipio').val();
          },
          dataSrc: 'data'
      },
      language: { url: '{{ asset('js/pt-BR.json') }}' },
      columns: [
          {
              data: 'id',
              render: (d) => `<input type="radio" name="conexaoSelecionada" value="${d}">`,
              className: 'text-center',
              width: '45px'
          },
          { data: 'nome_conexao', title: 'Conexão' },
          { data: 'provedor', title: 'Provedor' },
          { data: 'tecnologia', title: 'Tecnologia' },
          { data: 'ip_atingivel', title: 'IP' },
          { data: 'status', title: 'Status' },
          { data: 'nome_escola', title: 'Escola' },
          { data: 'municipio', title: 'Município' }
      ]
  });
$(document).on('change', '#tabela-hosts input[name="conexaoSelecionada"]', function () {
    window.conexaoSelecionada = $(this).val();
    $('#navDetalhes').removeClass('disabled');
});
  // 🔍 Aplicar filtros (reload server-side)
  $('#btnAplicarFiltros').on('click', function () {
      tabela.ajax.reload();
  });

  // 🔄 Limpar filtros
  $('#btnLimparFiltros').on('click', function () {
      $('#formFiltros')[0].reset();
      tabela.ajax.reload();
  });


});
</script>
@endsection
