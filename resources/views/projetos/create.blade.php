@extends('layouts.app')
@section('title', 'Cadastrar Projeto')

@section('content')
@section('breadcrumb')
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-3 py-2 rounded-3 shadow-sm">
      <li class="breadcrumb-item"><a href="{{ route('projetos.index') }}" class="text-decoration-none text-primary fw-semibold"><i class="fas fa-project-diagram me-1"></i> Projetos</a></li>
      <li class="breadcrumb-item active text-secondary fw-semibold">Novo Projeto</li>
    </ol>
  </nav>
@endsection
<div class="container-fluid">

    <div class="card card-default mt-3">

        <div class="card-header">
            <h3 class="card-title">Cadastrar projeto</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
        </div>

        <form action="{{ route('projetos.store') }}" method="POST" class="p-4">
            @csrf

            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control"
                           placeholder="Se vazio, será gerado automaticamente">
                </div>

                <div class="col-md-9">
                    <label class="form-label fw-semibold">Título do Projeto <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-control" required>
                </div>

                <!-- Contrato -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contrato Vinculado</label>
                    <select name="contrato_id" id="selectContrato" class="form-select">
                        <option value="">Carregando contratos...</option>
                    </select>
                </div>

                <!-- Item do contrato -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Item do Contrato</label>
                    <select name="itemcontrato_id" id="selectItensContrato" class="form-select">
                        <option value="">Selecione primeiro o contrato...</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Sistema</label>
                    <input type="text" name="sistema" value="{{ old('sistema') }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Módulo</label>
                    <input type="text" name="modulo" value="{{ old('modulo') }}" class="form-control">
                </div>

                <!-- Gerente técnico -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Gerente Técnico</label>
                    <select name="gerente_tecnico_id" class="form-select">
                        <option value="">— Selecionar —</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Gerente administrativo -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Gerente Administrativo</label>
                    <select name="gerente_adm_id" class="form-select">
                        <option value="">— Selecionar —</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- DRE -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">DRE Responsável</label>
                    <select name="dre_id" class="form-select">
                        <option value="">— Selecionar —</option>
                        @foreach ($dres as $d)
                            <option value="{{ $d->id }}">{{ $d->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Escola -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Escola (se aplicável)</label>
                    <select name="escola_id" class="form-select">
                        <option value="">— Selecionar —</option>
                        @foreach ($escolas as $e)
                            <option value="{{ $e->id }}">{{ $e->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Datas -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ old('data_inicio') }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Data Fim Prevista</label>
                    <input type="date" name="data_fim" value="{{ old('data_fim') }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Situação</label>
                    <select name="situacao" class="form-select">
                        @foreach ($situacoes as $key => $label)
                            <option value="{{ $key }}" @selected(old('situacao') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- APF Planejado -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">PF Planejado</label>
                    <input type="number" step="0.01" name="pf_planejado"
                           value="{{ old('pf_planejado') }}" class="form-control">
                </div>

                <!-- UST Planejada -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">UST Planejada</label>
                    <input type="number" step="0.01" name="ust_planejada"
                           value="{{ old('ust_planejada') }}" class="form-control">
                </div>

                <!-- Horas Planejadas -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Horas Planejadas</label>
                    <input type="number" name="horas_planejadas"
                           value="{{ old('horas_planejadas') }}" class="form-control">
                </div>

                <!-- Prioridade -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Prioridade</label>
                    <select name="prioridade" class="form-select">
                        @foreach ($prioridades as $key => $label)
                            <option value="{{ $key }}" @selected(old('prioridade') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tecnologia -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tecnologia Principal</label>
                    <select name="tecnologia" class="form-select">
                        <option value="">— Selecionar —</option>
                        @foreach ($tecnologias as $tec)
                            <option value="{{ $tec }}" @selected(old('tecnologia') == $tec)>{{ $tec }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Descrição -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição / Escopo do Projeto</label>
                    <textarea name="descricao" class="form-control" rows="4">{{ old('descricao') }}</textarea>
                </div>

                <!-- Botão -->
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('projetos.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> Cancelar</a>
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i>Salvar Projeto</button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function() {

    const selectContrato = document.getElementById("selectContrato");
    const selectItens = document.getElementById("selectItensContrato");

    // Carregar contratos da sua rota /ajax/contratos/0
    fetch('/ajax/contratos/0')
        .then(r => r.json())
        .then(contratos => {
            selectContrato.innerHTML = '<option value="">— Selecionar —</option>';
            contratos.forEach(c => {
                let texto = `${c.numero} — ${c.objeto.substring(0, 90)}`;
                selectContrato.innerHTML += `<option value="${c.id}">${texto}</option>`;
            });
        })
        .catch(err => {
            console.error(err);
            selectContrato.innerHTML = '<option value="">Erro ao carregar contratos</option>';
        });

    // Ao mudar contrato → carrega itens
    selectContrato.addEventListener('change', function() {
        const id = this.value;
        if (!id) {
            selectItens.innerHTML = '<option value="">Selecione primeiro o contrato...</option>';
            return;
        }

        fetch('/ajax/contratos/' + id)
            .then(r => r.json())
            .then(c => {
                if (!c.itens || !c.itens.length) {
                    selectItens.innerHTML = '<option value="">Contrato sem itens cadastrados</option>';
                    return;
                }
                selectItens.innerHTML = '<option value="">— Selecionar —</option>';
                c.itens.forEach(i => {
                    selectItens.innerHTML += `
                        <option value="${i.id}">
                            ${i.descricao.substring(0, 90)}
                        </option>
                    `;
                });
            })
            .catch(err => {
                console.error(err);
                selectItens.innerHTML = '<option value="">Erro ao carregar itens</option>';
            });
    });
});
</script>
@endsection
