@extends('layouts.app')

@section('plugins.Sweetalert2', true)
@section('title', 'Perfis de Usuário')

@section('content_body')
<div class="container-fluid">
    <!-- Área de notificações inline -->
    <div id="alertArea" class="mb-3" aria-live="polite"></div>
    <!-- 🔹 Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>
        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small">Nome</label>
                    <input type="text" id="filtroNome" class="form-control form-control-sm" placeholder="Nome do usuário">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small">CPF</label>
                    <input type="text" id="filtroCpf" class="form-control form-control-sm" placeholder="Somente números">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-secondary small">DRE</label>
                    <input type="text" id="filtroDre" class="form-control form-control-sm" placeholder="Nome da DRE">
                </div>
                <div class="col-md-3 d-flex justify-content-end align-items-end">
                    <div class="d-flex w-100">
                        <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                        <button type="button" id="btnLimpar" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-undo me-1"></i> Limpar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Lista -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Perfis de Usuários</h4>

        </div>
        <div class="card-body">
            <!-- 🔹 Navbar de ações -->
            <nav class="nav nav-pills mb-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a id="navDetalhes" class="nav-link disabled" href="#">
                            <i class="fas fa-eye text-info me-2"></i> Exibir Detalhes
                        </a>
                    </li>
                </ul>
            </nav>

            <table id="tabelaPerfis" class="table table-striped no-inner-borders w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:45px;"></th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Cargo</th>
                        <th>DRE</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('css')
@endsection

@push('js')
<script>
$(function() {
    let tabela;
    if ($.fn.dataTable.isDataTable('#tabelaPerfis')) {
        tabela = $('#tabelaPerfis').DataTable();
    } else {
        tabela = $('#tabelaPerfis').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "{{ route('user_profiles.index') }}",
            error: function(xhr) {
                let title = 'Erro ao carregar lista';
                let message = 'Tente novamente mais tarde.';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title, text: message });
                }
                const alertHtml = `
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> ${title}. ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                  </div>`;
                $('#alertArea').html(alertHtml);
            }
        },
        language: { url: window.DataTablesLangUrl },
        dom: 't<"bottom"p>',
        pageLength: 10,
        order: [[1, 'asc']],
        columns: [
            { data: 'id', visible: false },
            { data: 'nome_completo' },
            { data: 'cpf' },
            { data: 'cargo' },
            { data: 'dre' },
            { data: 'celular', defaultContent: '' }
        ]
    });
    }

    let perfilSelecionado = null;
    $('#tabelaPerfis').on('click', 'tbody tr', function () {
        const data = tabela.row(this).data();
        perfilSelecionado = data?.id;
        $('#navDetalhes').toggleClass('disabled', !perfilSelecionado);
    });

    $('#navDetalhes').on('click', function (e) {
        e.preventDefault();
        if (!perfilSelecionado) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'info', title: 'Seleção necessária', text: 'Selecione um perfil na tabela para visualizar os detalhes.' });
            }
            const alertHtml = `
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-1"></i> Selecione um perfil na tabela para visualizar os detalhes.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
              </div>`;
            $('#alertArea').html(alertHtml);
            return;
        }
        window.location.href = '{{ url('user_profiles') }}' + '/' + perfilSelecionado;
    });

    $('#btnAplicarFiltros').on('click', function () {
        tabela.column(1).search($('#filtroNome').val());
        tabela.column(2).search($('#filtroCpf').val());
        tabela.column(4).search($('#filtroDre').val());
        tabela.draw();
        const alertHtml = `
          <div class="alert alert-secondary alert-dismissible fade show" role="alert">
            <i class="fas fa-filter me-1"></i> Filtros aplicados.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
          </div>`;
        $('#alertArea').html(alertHtml);
    });
    $('#btnLimpar').on('click', function () {
        $('#formFiltros')[0].reset();
        tabela.search('').columns().search('');
        tabela.order([1, 'asc']);
        $('#navDetalhes').addClass('disabled');
        perfilSelecionado = null;
        const alertHtml = `
          <div class="alert alert-secondary alert-dismissible fade show" role="alert">
            <i class="fas fa-undo me-1"></i> Filtros limpos.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
          </div>`;
        $('#alertArea').html(alertHtml);
    });
});
</script>
@endpush
