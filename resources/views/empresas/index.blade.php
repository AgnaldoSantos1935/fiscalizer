@extends('layouts.app')
@section('title', 'Empresas Cadastradas')

@section('content_body')
<div class="container-fluid">

    <!-- 🔹 Card de Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>

        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm" method="GET" action="{{ route('empresas.index') }}">
                <div class="col-md-3">
                    <label for="filtroRazao" class="form-label fw-semibold text-secondary small">Razão Social</label>
                    <input type="text" id="filtroRazao" class="form-control form-control-sm" placeholder="Ex: Montreal, Prodepa...">
                </div>
                <div class="col-md-3">
                    <label for="filtroCNPJ" class="form-label fw-semibold text-secondary small">CNPJ</label>
                    <input type="text" id="filtroCNPJ" class="form-control form-control-sm" placeholder="Somente números">
                </div>
                <div class="col-md-3">
                    <label for="filtroCidade" class="form-label fw-semibold text-secondary small">Cidade</label>
                    <input type="text" id="filtroCidade" class="form-control form-control-sm" placeholder="Belém, Santarém...">
                </div>
                <div class="col-md-3">
                    <label for="filtroUF" class="form-label fw-semibold text-secondary small">UF</label>
                    <input type="text" id="filtroUF" maxlength="2" class="form-control form-control-sm" placeholder="PA, AM, MA...">
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="submit" id="btnAplicarFiltros" class="btn btn-primary btn-sm px-3 me-2">
                        <i class="fas fa-filter me-1"></i> Aplicar
                    </button>
                    <a href="{{ route('empresas.index') }}" id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Card Principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-building me-2 text-primary"></i>Empresas Cadastradas
            </h4>
        </div>

        <div class="card-body bg-white">
            <!-- 🔹 Navbar de ações -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item me-3">
                            <a id="navDetalhes" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-eye text-info me-1"></i> Detalhes
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <a id="navEditar" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-edit text-warning me-1"></i> Editar
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <a id="navExcluir" class="nav-link text-dark fw-semibold disabled" href="#">
                                <i class="fas fa-trash text-danger me-1"></i> Excluir
                            </a>
                        </li>
                    </ul>

                </div>
            </nav>

            <!-- 🔹 Tabela -->
            <table id="tabelaEmpresas" class="table table-striped no-inner-borders w-100">
                <thead class="bg-light text-secondary border-bottom">
                    <tr>
                        <th class="text-center" style="width: 45px;">#</th>
                        <th>Razão Social</th>
                        <th>CNPJ</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Cidade</th>
                        <th>UF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($empresas ?? []) as $e)
                        <tr>
                            <td class="text-center"><input type="radio" name="empresaSelecionada" value="{{ $e->id }}"></td>
                            <td class="fw-semibold text-dark">{{ $e->razao_social }}</td>
                            <td>{{ $e->cnpj }}</td>
                            <td class="text-muted small">{{ $e->email ?? '—' }}</td>
                            <td class="text-muted small">{{ $e->telefone ?? '—' }}</td>
                            <td>{{ $e->cidade ?? '—' }}</td>
                            <td>{{ $e->uf ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 🔹 Modal Detalhes -->
<div class="modal fade" id="modalDetalhesEmpresa" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title" id="modalLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalhes da Empresa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body bg-light">
                <table class="table table-borderless">
                    <tbody id="detalhesEmpresa"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.nav-link.disabled { opacity: .5; pointer-events: none; }
.table-borderless tbody tr:hover { background-color: #f8f9fa; transition: background-color .2s ease-in-out; }
</style>
@endsection

@push('js')
<script>
$(function(){
  let empresaSelecionada = null;
  $('#tabelaEmpresas').on('change','input[name="empresaSelecionada"]',function(){
    empresaSelecionada = $(this).val();
    $('#navDetalhes, #navEditar, #navExcluir').removeClass('disabled');
  });
  $('#navEditar').on('click',function(e){
    e.preventDefault();
    if (!empresaSelecionada) return;
    window.location.href = '/empresas/' + empresaSelecionada + '/edit';
  });
  $('#navExcluir').on('click',function(e){
    e.preventDefault();
    if (!empresaSelecionada) return;
    if (!confirm('Deseja realmente excluir esta empresa?')) return;
    fetch('/empresas/' + empresaSelecionada, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(() => location.reload());
  });
  $('#navDetalhes').on('click',function(e){
    e.preventDefault();
    if (!empresaSelecionada) return;
    fetch('{{ url("empresas") }}' + '/' + empresaSelecionada, { headers: { 'Accept':'application/json' } })
      .then(r => r.json())
      .then(data => {
        const e = data.empresa;
        const enderecoParts = [e.logradouro, e.numero, e.complemento, e.bairro].filter(v => !!v);
        const enderecoTxt = enderecoParts.length ? enderecoParts.join(', ') : (e.endereco ?? '-');
        $('#detalhesEmpresa').html(`
          <tr><th>Razão Social</th><td>${e.razao_social}</td></tr>
          <tr><th>CNPJ</th><td>${e.cnpj}</td></tr>
          <tr><th>Email</th><td>${e.email ?? '-'}</td></tr>
          <tr><th>Telefone</th><td>${e.telefone ?? '-'}</td></tr>
          <tr><th>Cidade</th><td>${e.cidade ?? '-'}</td></tr>
          <tr><th>UF</th><td>${e.uf ?? '-'}</td></tr>
          <tr><th>Endereço</th><td>${enderecoTxt}</td></tr>
        `);
        new bootstrap.Modal(document.getElementById('modalDetalhesEmpresa')).show();
      });
  });
});
</script>
@endpush
