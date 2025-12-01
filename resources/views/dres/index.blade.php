@extends('layouts.app')

@section('title', 'Diretorias Regionais de Educação')

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
            <form id="formFiltros" class="row g-3 mb-3 bg-light p-3 rounded-4 shadow-sm" method="GET" action="{{ route('dres.index') }}">
                <div class="col-md-2">
                    <label for="filtroCodigo" class="form-label fw-semibold text-secondary small">Código DRE</label>
                    <input type="text" id="filtroCodigo" name="codigo" value="{{ request('codigo') }}" class="form-control form-control-sm" placeholder="Ex: 01, 05, 12...">
                </div>

                <div class="col-md-4">
                    <label for="filtroNome" class="form-label fw-semibold text-secondary small">Nome da DRE</label>
                    <input type="text" id="filtroNome" name="nome" value="{{ request('nome') }}" class="form-control form-control-sm" placeholder="Digite parte do nome">
                </div>

                <div class="col-md-3">
                    <label for="filtroMunicipio" class="form-label fw-semibold text-secondary small">Município Sede</label>
                    <select id="filtroMunicipio" name="municipio" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach(($municipios ?? []) as $m)
                            <option value="{{ $m }}" @selected(request('municipio')===$m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="filtroEmail" class="form-label fw-semibold text-secondary small">E-mail Institucional</label>
                    <input type="text" id="filtroEmail" name="email" value="{{ request('email') }}" class="form-control form-control-sm" placeholder="@seduc.pa.gov.br">
                </div>

                <div class="col-md-2">
                    <label for="filtroUF" class="form-label fw-semibold text-secondary small">UF</label>
                    <select id="filtroUF" name="uf" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach(($ufs ?? []) as $uf)
                            <option value="{{ $uf }}" @selected(request('uf')===$uf)>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="filtroCEP" class="form-label fw-semibold text-secondary small">CEP</label>
                    <input type="text" id="filtroCEP" name="cep" value="{{ request('cep') }}" class="form-control form-control-sm" placeholder="00000-000" maxlength="9">
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="submit" id="btnAplicarFiltros" class="btn btn-primary btn-sm px-3 me-2">
                        <i class="fas fa-filter me-1"></i> Aplicar
                    </button>
                    <a href="{{ route('dres.index') }}" id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="fas fa-undo me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 Card principal -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-map-marked-alt me-2 text-primary"></i>Diretorias Regionais de Educação
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
            <table id="tabelaDres" class="table table-striped no-inner-borders w-100">
                <thead class="bg-light text-secondary border-bottom">
                    <tr>
                        <th class="text-center" style="width: 45px;">#</th>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Município Sede</th>
                        <th>UF</th>
                        <th>CEP</th>
                        <th>Email</th>
                        <th>Telefone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dres as $dre)
                    <tr class="hoverable-row">
                        <td class="text-center">
                            <input type="radio" name="dreSelecionada" value="{{ $dre->id }}">
                        </td>
                        <td>{{ $dre->codigodre }}</td>
                        <td class="fw-semibold text-dark">{{ $dre->nome_dre }}</td>
                        <td>{{ $dre->municipio_sede }}</td>
                        <td><span class="badge bg-secondary">{{ $dre->uf ?? '—' }}</span></td>
                        <td><span class="badge bg-light text-secondary">{{ $dre->cep ?? '—' }}</span></td>
                        <td class="text-muted small">{{ $dre->email }}</td>
                        <td class="text-muted small">{{ $dre->telefone }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">{{ $dres->links() }}</div>
        </div>
    </div>
</div>

<!-- 🔹 Modal Detalhes -->
<div class="modal fade" id="modalDetalhesDre" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title" id="modalLabel">
                    <i class="fas fa-info-circle me-2"></i>Detalhes da DRE
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body bg-light">
                <table class="table table-borderless">
                    <tbody id="detalhesDre"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.table-borderless tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease-in-out;
}
.nav-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}
.nav-link {
    transition: color 0.2s ease-in-out;
}
.nav-link:hover {
    color: #0d6efd !important;
}
#formFiltros input {
    border-radius: 10px;
}
#formFiltros .btn {
    border-radius: 20px;
}
/* Garante prioridade de exibição do modal */
.modal-backdrop.show { z-index: 1040 !important; }
.modal { z-index: 1050 !important; }
</style>
@endsection

@push('js')



<script>
$(function(){
    let dreSelecionada = null;

    // Seleção via radio
    $('#tabelaDres').on('change', 'input[name="dreSelecionada"]', function() {
        dreSelecionada = $(this).val();
        $('#navDetalhes, #navEditar, #navExcluir').removeClass('disabled');
    });

    // 🔹 Detalhes
    $('#navDetalhes').on('click', function(e) {
        e.preventDefault();
        if (!dreSelecionada) return;

        fetch('{{ url("dres") }}/' + dreSelecionada)
            .then(resp => resp.json())
            .then(data => {
                const d = data.dre;
                const enderecoParts = [d.logradouro, d.numero, d.complemento, d.bairro].filter(v => !!v);
                const enderecoTxt = enderecoParts.length ? enderecoParts.join(', ') : (d.endereco ?? '-');
                $('#detalhesDre').html(`
                    <tr><th>Código</th><td>${d.codigodre}</td></tr>
                    <tr><th>Nome</th><td>${d.nome_dre}</td></tr>
                    <tr><th>Município</th><td>${d.municipio_sede}</td></tr>
                    <tr><th>Email</th><td>${d.email ?? '-'}</td></tr>
                    <tr><th>Telefone</th><td>${d.telefone ?? '-'}</td></tr>
                    <tr><th>UF</th><td>${d.uf ?? '-'}</td></tr>
                    <tr><th>CEP</th><td>${d.cep ?? '-'}</td></tr>
                    <tr><th>Endereço</th><td>${enderecoTxt}</td></tr>
                `);
                const modalDetalhes = new bootstrap.Modal(document.getElementById('modalDetalhesDre'));
                modalDetalhes.show();
            })
            .catch(err => console.error('Erro ao carregar detalhes:', err));
    });

    // 🔹 Editar
    $('#navEditar').on('click', function(e) {
        e.preventDefault();
        if (dreSelecionada) {
            window.location.href = '/dres/' + dreSelecionada + '/edit';
        }
    });

    // 🔹 Excluir
    $('#navExcluir').on('click', function(e) {
        e.preventDefault();
        if (!dreSelecionada) return;
        if (confirm('Tem certeza que deseja excluir esta DRE?')) {
            fetch('{{ url("dres") }}/' + dreSelecionada, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao excluir');
                return response.json();
            })
            .then(() => {
                alert('DRE excluída com sucesso!');
                location.reload();
            })
            .catch(error => {
                console.error('Erro ao excluir DRE:', error);
                alert('Não foi possível excluir a DRE.');
            });
        }
    });

    $('#filtroCEP').on('input', function(){
        const v = (this.value||'').replace(/\D/g,'').slice(0,8);
        this.value = v.length>5? v.slice(0,5)+'-'+v.slice(5): v;
    });
});
</script>
@endpush
