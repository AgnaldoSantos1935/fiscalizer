@extends('layouts.app')

@section('title', 'Documentos')

@section('content_body')
<div class="container-fluid">
    <!-- 🔹 Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-search me-2 text-primary"></i>Filtros de Pesquisa
            </h4>
        </div>
        <div class="card-body bg-white">
            <form id="formFiltros" class="row g-3 bg-light p-3 rounded-4 shadow-sm align-items-end mb-3" method="GET" action="{{ route('documentos.index') }}">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Tipo</label>
                    <input type="text" id="filtroTipo" class="form-control form-control-sm" placeholder="Ex: NF, Relatório...">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Contrato</label>
                    <input type="text" id="filtroContrato" class="form-control form-control-sm" placeholder="Número ou ID do contrato">
                </div>
                <div class="col-md-4 d-flex justify-content-end align-items-end">
                    <div class="d-flex w-100">
                        <button type="submit" id="btnAplicarFiltros" class="btn btn-primary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('documentos.index') }}" id="btnLimpar" class="btn btn-outline-secondary btn-sm btn-sep flex-grow-1">
                            <i class="fas fa-undo me-1"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Lista -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Documentos</h4>
            
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
                    <li class="nav-item">
                        <a id="navEditar" class="nav-link disabled" href="#">
                            <i class="fas fa-edit text-warning me-2"></i> Editar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="navExcluir" class="nav-link disabled" href="#">
                            <i class="fas fa-trash text-danger me-2"></i> Excluir
                        </a>
                    </li>
                </ul>
            </nav>

            <table id="tabelaDocumentos" class="table table-striped no-inner-borders w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:45px;"></th>
                        <th>Tipo</th>
                        <th>Título</th>
                        <th>Contrato</th>
                        <th>Data Upload</th>
                        <th>Versão</th>
                        <th>Arquivo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($documentos ?? []) as $d)
                        @php
                            $path = $d->caminho_arquivo;
                            $ext = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));
                            $icon = 'fa-file'; $color = '';
                            switch ($ext) {
                                case 'pdf': $icon = 'fa-file-pdf'; $color = 'text-danger'; break;
                                case 'doc': case 'docx': $icon = 'fa-file-word'; $color = 'text-primary'; break;
                                case 'xls': case 'xlsx': $icon = 'fa-file-excel'; $color = 'text-success'; break;
                                case 'ppt': case 'pptx': $icon = 'fa-file-powerpoint'; $color = 'text-danger'; break;
                                case 'zip': case 'rar': $icon = 'fa-file-archive'; $color = 'text-warning'; break;
                                case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp': $icon = 'fa-file-image'; $color = 'text-info'; break;
                                case 'txt': $icon = 'fa-file-alt'; break;
                            }
                        @endphp
                        <tr>
                            <td class="text-center"><input type="radio" name="docSelecionado" value="{{ $d->id }}"></td>
                            <td>{{ $d->tipo }}</td>
                            <td>{{ $d->titulo ?? '—' }}</td>
                            <td>{{ $d->contrato->numero ?? $d->contrato_id }}</td>
                            <td>{{ $d->data_upload ?? '—' }}</td>
                            <td>{{ $d->versao ?? '—' }}</td>
                            <td>
                                @if($path)
                                    <a href="{{ route('documentos.visualizar', $d->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-eye"></i> Abrir
                                    </a>
                                    <a href="{{ route('documentos.download', $d->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas {{ $icon }} {{ $color }}"></i> Download
                                    </a>
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
@endsection

@section('css')
<style>
.nav-link.disabled { opacity: 0.5; pointer-events: none; }
</style>
@endsection

@push('js')
<script>
$(function(){
  let selecionado = null;
  $('#tabelaDocumentos').on('change','input[name="docSelecionado"]',function(){
    selecionado = $(this).val();
    $('#navDetalhes, #navEditar, #navExcluir').removeClass('disabled');
  });
  $('#navDetalhes').on('click',function(e){
    e.preventDefault();
    if (!selecionado) return;
    window.location.href = '{{ url('documentos') }}' + '/' + selecionado;
  });
  $('#navEditar').on('click',function(e){
    e.preventDefault();
    if (!selecionado) return;
    window.location.href = '{{ url('documentos') }}' + '/' + selecionado + '/edit';
  });
  $('#navExcluir').on('click',function(e){
    e.preventDefault();
    if (!selecionado) return;
    if (!confirm('Deseja realmente excluir este documento?')) return;
    fetch('{{ url('documentos') }}' + '/' + selecionado, { method:'DELETE', headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' } })
      .then(() => location.reload());
  });
});
</script>
@endpush
