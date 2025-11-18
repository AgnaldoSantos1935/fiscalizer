@extends('layouts.app')
@section('plugins.Summernote', true)
@section('title', 'Novo Termo de Referência')

@section('content_body')
<div class="container-fluid">
    @php
        $user = auth()->user();
        $profile = \App\Models\UserProfile::where('user_id', $user?->id)->first();
        $pessoa = optional($user)->pessoa;
        $session_responsavel = $profile->nome_completo ?? $user?->name ?? '';
        $session_cargo = $profile->cargo ?? (optional($user?->role)->nome ?? '');
        $session_matricula = $profile->matricula ?? '';
        $session_cidade = $pessoa->cidade ?? ($profile->cidade ?? 'Belém');
    @endphp
    <form action="{{ route('contratacoes.termos-referencia.store') }}" method="POST" class="card shadow-sm border-0 rounded-4">
        @csrf
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-secondary fw-semibold">
                <i class="fas fa-file-alt text-primary me-2"></i>Novo Termo de Referência
            </h4>
            <a href="{{ route('contratacoes.termos-referencia.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
        </div>
        <div class="card-body bg-white">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo de TR</label>
                    <select name="tipo_tr" class="form-select">
                        <option value="">Selecione...</option>
                        <option value="bens_comuns" {{ old('tipo_tr')=='bens_comuns' ? 'selected' : '' }}>Bens Comuns</option>
                        <option value="servicos_sem_mao_de_obra_sem_prorrogacao" {{ old('tipo_tr')=='servicos_sem_mao_de_obra_sem_prorrogacao' ? 'selected' : '' }}>Serviços sem Mão-de-Obra e sem Prorrogação</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">PAE nº</label>
                    <input type="text" name="pae_numero" class="form-control" value="{{ old('pae_numero') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="cidade" class="form-control" value="{{ old('cidade', $session_cidade) }}" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Data de Emissão</label>
                    <input type="date" name="data_emissao" class="form-control" value="{{ old('data_emissao') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Responsável</label>
                    <input type="text" name="responsavel_nome" class="form-control" value="{{ old('responsavel_nome', $session_responsavel) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="responsavel_cargo" class="form-control" value="{{ old('responsavel_cargo', $session_cargo) }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Matrícula</label>
                    <input type="text" name="responsavel_matricula" class="form-control" value="{{ old('responsavel_matricula', $session_matricula) }}" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Valor Estimado</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" name="valor_estimado" class="form-control input-money" placeholder="0,00" readonly>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <small class="text-muted">Calculado automaticamente</small>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="estimado_override">
                            <label class="form-check-label" for="estimado_override">Editar manualmente</label>
                        </div>
                    </div>
                    <div id="estimado_diff_alert" class="alert alert-warning py-2 px-3 mt-2 d-none">Valor estimado difere do total dos itens. Verifique.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="rascunho" selected>Rascunho</option>
                        <option value="em_analise">Em análise</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
            </div>

            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Definição do objeto</h5>
                </div>
                <div class="card-body">
                    <textarea name="objeto" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Fundamentação da contratação</h5>
                </div>
                <div class="card-body">
                    <textarea name="justificativa" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Descrição da solução</h5>
                </div>
                <div class="card-body">
                    <textarea name="escopo" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Requisitos da contratação</h5>
                </div>
                <div class="card-body">
                    <textarea name="requisitos" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <!-- Campos específicos para bens comuns -->
            <div id="bens_campos" class="card card-outline card-secondary mb-3" style="display:none;">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Compra de bens (se aplicável)</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-secondary">
                        Quando se tratar de compra de bens, inclua:
                    </div>
                    <div class="mb-3">
                        <label class="form-label">a) Especificação do produto, requisitos de qualidade, rendimento, compatibilidade, durabilidade e segurança</label>
                        <textarea name="especificacao_produto" class="form-control tr-editor" rows="8"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">b) Indicação dos locais de entrega e regras para recebimento (provisório e definitivo)</label>
                        <textarea name="locais_entrega_recebimento" class="form-control tr-editor" rows="8"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">c) Especificação da garantia exigida e condições de manutenção e assistência técnica</label>
                        <textarea name="garantia_manutencao_assistencia" class="form-control tr-editor" rows="8"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Marcação Sim/Não (bens)</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-2 fw-semibold">Garantia exigida?</div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="garantia_exigida" id="garantia_exigida_sim" value="1" {{ old('garantia_exigida')==='1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="garantia_exigida_sim">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="garantia_exigida" id="garantia_exigida_nao" value="0" {{ old('garantia_exigida')==='0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="garantia_exigida_nao">Não</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2 fw-semibold">Prevê manutenção?</div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="manutencao_incluida" id="manutencao_incluida_sim" value="1" {{ old('manutencao_incluida')==='1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="manutencao_incluida_sim">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="manutencao_incluida" id="manutencao_incluida_nao" value="0" {{ old('manutencao_incluida')==='0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="manutencao_incluida_nao">Não</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-2 fw-semibold">Prevê assistência técnica?</div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assistencia_tecnica_incluida" id="assistencia_tecnica_incluida_sim" value="1" {{ old('assistencia_tecnica_incluida')==='1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="assistencia_tecnica_incluida_sim">Sim</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assistencia_tecnica_incluida" id="assistencia_tecnica_incluida_nao" value="0" {{ old('assistencia_tecnica_incluida')==='0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="assistencia_tecnica_incluida_nao">Não</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Critérios de Julgamento</h5>
                </div>
                <div class="card-body">
                    <textarea name="criterios_julgamento" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Prazos</h5>
                </div>
                <div class="card-body">
                    <textarea name="prazos" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Local de Execução</h5>
                </div>
                <div class="card-body">
                    <textarea name="local_execucao" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Critérios de medição e pagamento</h5>
                </div>
                <div class="card-body">
                    <textarea name="criterios_medicao_pagamento" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Forma e critérios de seleção do fornecedor</h5>
                </div>
                <div class="card-body">
                    <textarea name="forma_criterios_selecao_fornecedor" class="form-control tr-editor" rows="10"></textarea>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">5.1 - Prova de Qualidade (ref. 5.1)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Haverá prova de qualidade?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="prova_qualidade" id="prova_qualidade_sim" value="1" {{ old('prova_qualidade')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="prova_qualidade_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="prova_qualidade" id="prova_qualidade_nao" value="0" {{ old('prova_qualidade')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="prova_qualidade_nao">Não</label>
                    </div>
                </div>
                <div id="prova_qualidade_justificativa_group" class="mt-2" style="display:none;">
                    <label class="form-label">Justificativa (caso se assinale "Sim")</label>
                    <textarea name="prova_qualidade_justificativa" class="form-control form-control-sm" rows="5">{{ old('prova_qualidade_justificativa') }}</textarea>
                </div>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">5.2 - Amostra (ref. 5.2)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">O edital exigirá amostra?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="edital_exigira_amostra" id="edital_exigira_amostra_sim" value="1" {{ old('edital_exigira_amostra')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="edital_exigira_amostra_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="edital_exigira_amostra" id="edital_exigira_amostra_nao" value="0" {{ old('edital_exigira_amostra')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="edital_exigira_amostra_nao">Não</label>
                    </div>
                </div>
                <div id="edital_amostra_justificativa_group" class="mt-2" style="display:none;">
                    <label class="form-label">Justificativa (caso se assinale "Sim")</label>
                    <textarea name="edital_amostra_justificativa" class="form-control form-control-sm" rows="5">{{ old('edital_amostra_justificativa') }}</textarea>
                </div>
                </div>
                </div>
            </div>
            <div id="bens_condicoes_avancadas" class="mb-3" style="display:none;">
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">5.3 - Garantia do Bem (ref. 5.3)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label">Haverá garantia do bem?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="garantia_bem" id="garantia_bem_sim" value="1" {{ old('garantia_bem')==='1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="garantia_bem_sim">Sim</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="garantia_bem" id="garantia_bem_nao" value="0" {{ old('garantia_bem')==='0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="garantia_bem_nao">Não</label>
                        </div>
                    </div>
                    <div id="garantia_bem_campos" class="row mt-2" style="display:none;">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Itens indicados (X, Y, ...)</label>
                            <input type="text" name="garantia_bem_itens" class="form-control" value="{{ old('garantia_bem_itens') }}" placeholder="Ex.: Itens X e Y">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Prazo mínimo de garantia (meses)</label>
                            <input type="number" min="0" step="1" name="garantia_bem_meses" class="form-control" value="{{ old('garantia_bem_meses') }}" placeholder="Ex.: 12">
                        </div>
                    </div>
                    </div>
                </div>
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">5.4 - Assistência Técnica (ref. 5.4)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label">Haverá assistência técnica?</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="assistencia_tecnica_tipo" id="assistencia_tecnica_credenciada" value="credenciada" {{ old('assistencia_tecnica_tipo')==='credenciada' ? 'checked' : '' }}>
                            <label class="form-check-label" for="assistencia_tecnica_credenciada">Sim, por empresa credenciada</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="assistencia_tecnica_tipo" id="assistencia_tecnica_propria" value="propria" {{ old('assistencia_tecnica_tipo')==='propria' ? 'checked' : '' }}>
                            <label class="form-check-label" for="assistencia_tecnica_propria">Sim, por meios próprios</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="assistencia_tecnica_tipo" id="assistencia_tecnica_nao" value="nao" {{ old('assistencia_tecnica_tipo')==='nao' ? 'checked' : '' }}>
                            <label class="form-check-label" for="assistencia_tecnica_nao">Não será prestada</label>
                        </div>
                    </div>
                    <div id="assistencia_tecnica_meses_group" class="mt-2" style="display:none;">
                        <label class="form-label">Duração da assistência técnica (meses)</label>
                        <input type="number" min="0" step="1" name="assistencia_tecnica_meses" class="form-control" value="{{ old('assistencia_tecnica_meses') }}" placeholder="Ex.: 12">
                    </div>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
<h5 class="mb-0">6.1 Forma de Contratação (ref. 6.1)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Forma de contratação</label>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="forma_contratacao" id="forma_inexigibilidade_art74_y" value="inexigibilidade_art74_y" {{ old('forma_contratacao')==='inexigibilidade_art74_y' ? 'checked' : '' }}>
                        <label class="form-check-label" for="forma_inexigibilidade_art74_y">Inexigibilidade (art. 74, Y, Lei 14.133/21)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="forma_contratacao" id="forma_dispensa_valor_art75_ii" value="dispensa_valor_art75_ii" {{ old('forma_contratacao')==='dispensa_valor_art75_ii' ? 'checked' : '' }}>
                        <label class="form-check-label" for="forma_dispensa_valor_art75_ii">Dispensa por valor (art. 75, II)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="forma_contratacao" id="forma_dispensa_art75_y" value="dispensa_art75_y" {{ old('forma_contratacao')==='dispensa_art75_y' ? 'checked' : '' }}>
                        <label class="form-check-label" for="forma_dispensa_art75_y">Dispensa (art. 75, Y)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="forma_contratacao" id="forma_pregao_eletronico" value="pregao_eletronico" {{ old('forma_contratacao')==='pregao_eletronico' ? 'checked' : '' }}>
                        <label class="form-check-label" for="forma_pregao_eletronico">Pregão eletrônico</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="forma_contratacao" id="forma_concorrencia" value="concorrencia" {{ old('forma_contratacao')==='concorrencia' ? 'checked' : '' }}>
                        <label class="form-check-label" for="forma_concorrencia">Concorrência</label>
                    </div>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
<h5 class="mb-0">6.2 Critério de Julgamento (ref. 6.2)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Critério de julgamento</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="criterio_julgamento_tipo" id="criterio_menor_preco" value="menor_preco" {{ old('criterio_julgamento_tipo')==='menor_preco' ? 'checked' : '' }}>
                        <label class="form-check-label" for="criterio_menor_preco">Menor preço</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="criterio_julgamento_tipo" id="criterio_maior_desconto" value="maior_desconto" {{ old('criterio_julgamento_tipo')==='maior_desconto' ? 'checked' : '' }}>
                        <label class="form-check-label" for="criterio_maior_desconto">Maior desconto</label>
                    </div>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
<h5 class="mb-0">6.3 Orçamento Sigiloso (ref. 6.3)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">O orçamento estimado é sigiloso?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="orcamento_sigiloso" id="orcamento_sigiloso_sim" value="1" {{ old('orcamento_sigiloso')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="orcamento_sigiloso_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="orcamento_sigiloso" id="orcamento_sigiloso_nao" value="0" {{ old('orcamento_sigiloso')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="orcamento_sigiloso_nao">Não</label>
                    </div>
                </div>
                <div id="orcamento_sigiloso_justificativa_group" class="mt-2" style="display:none;">
                    <label class="form-label">Justificativa (caso se assinale "Sim")</label>
                    <textarea name="orcamento_sigiloso_justificativa" class="form-control form-control-sm" rows="5">{{ old('orcamento_sigiloso_justificativa') }}</textarea>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">6.4 - Critério para a proposta ser aceita (ref. 6.4)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Critério para a proposta ser aceita</label>
                <div class="alert alert-light border">
                    A proposta deve observar os valores unitários e global máximos aceitáveis conforme planilha de composição de preços do orçamento estimado.
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
<h5 class="mb-0">6.5 Itens Exclusivos ME/EPP (ref. 6.5)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Há itens com participação exclusiva para ME/EPP?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="itens_exclusivos_me_epp" id="itens_exclusivos_sim" value="1" {{ old('itens_exclusivos_me_epp')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="itens_exclusivos_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="itens_exclusivos_me_epp" id="itens_exclusivos_nao" value="0" {{ old('itens_exclusivos_me_epp')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="itens_exclusivos_nao">Não</label>
                    </div>
                </div>
                <div id="itens_exclusivos_lista_group" class="mt-2" style="display:none;">
                    <label class="form-label">Indicar os itens (caso se assinale "Sim")</label>
                    <textarea name="itens_exclusivos_lista" class="form-control form-control-sm" rows="5">{{ old('itens_exclusivos_lista') }}</textarea>
                </div>
                </div>
            </div>
            <hr class="my-4" />
            <h5 class="text-secondary fw-semibold mb-3"><i class="fas fa-check-circle text-primary me-2"></i>7 - Habilitação e Qualificações</h5>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">7.1 - Habilitação Jurídica (ref. 7.1)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Qual será a habilitação jurídica exigida?</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="habilitacao_juridica_existencia" id="habilitacao_juridica_existencia" value="1" {{ old('habilitacao_juridica_existencia') ? 'checked' : '' }}>
                        <label class="form-check-label" for="habilitacao_juridica_existencia">Comprovação de existência jurídica.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="habilitacao_juridica_autorizacao" id="habilitacao_juridica_autorizacao" value="1" {{ old('habilitacao_juridica_autorizacao') ? 'checked' : '' }}>
                        <label class="form-check-label" for="habilitacao_juridica_autorizacao">Autorização para o exercício da atividade.</label>
                    </div>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">7.2 - Habilitação Técnica (ref. 7.2)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Será exigida habilitação técnica?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="habilitacao_tecnica_exigida" id="habilitacao_tecnica_sim" value="1" {{ old('habilitacao_tecnica_exigida')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="habilitacao_tecnica_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="habilitacao_tecnica_exigida" id="habilitacao_tecnica_nao" value="0" {{ old('habilitacao_tecnica_exigida')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="habilitacao_tecnica_nao">Não</label>
                    </div>
                </div>
                <div id="habilitacao_tecnica_campos" class="row mt-2" style="display:none;">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Qual?</label>
                        <textarea name="habilitacao_tecnica_qual" class="form-control form-control-sm" rows="4">{{ old('habilitacao_tecnica_qual') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Por que?</label>
                        <textarea name="habilitacao_tecnica_justificativa" class="form-control form-control-sm" rows="4">{{ old('habilitacao_tecnica_justificativa') }}</textarea>
                    </div>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
<h5 class="mb-0">7.3 Qualificações Técnicas Exigidas (ref. 7.3)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Qualificações técnicas exigidas</label>
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="qt_declaracao_ciencia" id="qt_declaracao_ciencia" value="1" {{ old('qt_declaracao_ciencia') ? 'checked' : '' }}>
                            <label class="form-check-label" for="qt_declaracao_ciencia">Declaração de ciência das informações necessárias para o cumprimento da futura obrigação contratual.</label>
                        </div>
                        <div id="qt_declaracao_justificativa_group" class="mt-2" style="display:none;">
                            <label class="form-label">Justificativa</label>
                            <textarea name="qt_declaracao_justificativa" class="form-control form-control-sm" rows="4">{{ old('qt_declaracao_justificativa') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="qt_registro_entidade" id="qt_registro_entidade" value="1" {{ old('qt_registro_entidade') ? 'checked' : '' }}>
                            <label class="form-check-label" for="qt_registro_entidade">Registro na entidade profissional competente.</label>
                        </div>
                        <div id="qt_registro_justificativa_group" class="mt-2" style="display:none;">
                            <label class="form-label">Justificativa</label>
                            <textarea name="qt_registro_justificativa" class="form-control form-control-sm" rows="4">{{ old('qt_registro_justificativa') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="qt_indicacao_pessoal" id="qt_indicacao_pessoal" value="1" {{ old('qt_indicacao_pessoal') ? 'checked' : '' }}>
                            <label class="form-check-label" for="qt_indicacao_pessoal">Indicação de pessoal técnico, instalações e aparelhamento... (comprovação da qualificação técnica da equipe).</label>
                        </div>
                        <div id="qt_indicacao_justificativa_group" class="mt-2" style="display:none;">
                            <label class="form-label">Justificativa</label>
                            <textarea name="qt_indicacao_justificativa" class="form-control form-control-sm" rows="4">{{ old('qt_indicacao_justificativa') }}</textarea>
                        </div>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="qt_outro" id="qt_outro" value="1" {{ old('qt_outro') ? 'checked' : '' }}>
                            <label class="form-check-label" for="qt_outro">Outro previsto em lei especial.</label>
                        </div>
                        <div id="qt_outro_campos" class="row mt-2" style="display:none;">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Especificar</label>
                                <textarea name="qt_outro_especificar" class="form-control form-control-sm" rows="4">{{ old('qt_outro_especificar') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Justificativa</label>
                                <textarea name="qt_outro_justificativa" class="form-control form-control-sm" rows="4">{{ old('qt_outro_justificativa') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="qt_nao_exigida" id="qt_nao_exigida" value="1" {{ old('qt_nao_exigida') ? 'checked' : '' }}>
                        <label class="form-check-label" for="qt_nao_exigida">Não será exigida prova de qualificação técnica em razão da baixa complexidade da contratação.</label>
                    </div>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
<h5 class="mb-0">7.4 Sustentabilidade (ref. 7.4)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Há critério de sustentabilidade?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="criterio_sustentabilidade" id="criterio_sustentabilidade_sim" value="1" {{ old('criterio_sustentabilidade')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="criterio_sustentabilidade_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="criterio_sustentabilidade" id="criterio_sustentabilidade_nao" value="0" {{ old('criterio_sustentabilidade')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="criterio_sustentabilidade_nao">Não</label>
                    </div>
                </div>
                <div id="criterio_sustentabilidade_especificar_group" class="mt-2" style="display:none;">
                    <label class="form-label">Especificar (caso se assinale "Sim")</label>
                    <textarea name="criterio_sustentabilidade_especificar" class="form-control form-control-sm" rows="4">{{ old('criterio_sustentabilidade_especificar') }}</textarea>
                </div>
                </div>
            </div>
            <div class="card card-outline card-secondary mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">7.5 - Riscos Assumidos pela Contratada (ref. 7.5)</h5>
                </div>
                <div class="card-body">
                <label class="form-label">Há riscos a serem assumidos pela contratada?</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="riscos_assumidos_contratada" id="riscos_assumidos_sim" value="1" {{ old('riscos_assumidos_contratada')==='1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="riscos_assumidos_sim">Sim</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="riscos_assumidos_contratada" id="riscos_assumidos_nao" value="0" {{ old('riscos_assumidos_contratada')==='0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="riscos_assumidos_nao">Não</label>
                    </div>
                </div>
                <div id="riscos_assumidos_especificar_group" class="mt-2" style="display:none;">
                    <label class="form-label">Especificar (caso se assinale "Sim")</label>
                    <textarea name="riscos_assumidos_especificar" class="form-control form-control-sm" rows="4">{{ old('riscos_assumidos_especificar') }}</textarea>
                </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Modelo de execução do objeto</label>
                <textarea name="modelo_execucao" class="form-control tr-editor" rows="10"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Modelo de gestão do contrato</label>
                <textarea name="modelo_gestao" class="form-control tr-editor" rows="10"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Forma de Pagamento</label>
                <textarea name="forma_pagamento" class="form-control tr-editor" rows="10"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Estimativas do valor de contratação (texto)</label>
                <textarea name="estimativas_valor_texto" class="form-control tr-editor" rows="8"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Adequação orçamentária</label>
                <textarea name="adequacao_orcamentaria" class="form-control tr-editor" rows="8"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Adequação orçamentária confirmada?</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="adequacao_orcamentaria_confirmada" id="adequacao_confirmada_sim" value="1" {{ old('adequacao_orcamentaria_confirmada')==='1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="adequacao_confirmada_sim">Sim</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="adequacao_orcamentaria_confirmada" id="adequacao_confirmada_nao" value="0" {{ old('adequacao_orcamentaria_confirmada')==='0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="adequacao_confirmada_nao">Não</label>
                </div>
            </div>
            

            <hr class="my-4" />
            <h5 class="text-secondary fw-semibold mb-3"><i class="fas fa-list-ul text-primary me-2"></i>Itens do Termo de Referência</h5>

            <div class="table-responsive mb-3">
                <table class="table table-striped align-middle" id="itens-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descrição</th>
                            <th>Unidade</th>
                            <th class="text-end">Quantidade</th>
                            <th class="text-end">Valor Unitário (R$)</th>
                            <th class="text-end">Valor Total (R$)</th>
                            <th class="text-center" style="width: 90px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="itens-body"></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total dos Itens</th>
                            <th class="text-end" id="total-itens">R$ 0,00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-item"><i class="fas fa-plus-circle me-1"></i>Adicionar Item</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-clear-items"><i class="fas fa-trash-alt me-1"></i>Limpar Itens</button>
            </div>
        </div>
        <!-- Seções 8 e 9 -->
        <div class="card card-outline card-secondary mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">8 - Entrega e Recebimento do Bem</h5>
            </div>
            <div class="card-body">
                <!-- 8.1 - Como o bem deve ser entregue? -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">8.1 Forma de Entrega (ref. 8.1)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Como o bem deve ser entregue?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entrega_forma" id="entrega_total" value="total" @checked(old('entrega_forma')==='total')>
                        <label class="form-check-label" for="entrega_total">O bem deve ser totalmente entregue de uma só vez, conforme edital.</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entrega_forma" id="entrega_parcelada" value="parcelada" @checked(old('entrega_forma')==='parcelada')>
                        <label class="form-check-label" for="entrega_parcelada">O bem deve ser entregue em parcelas.</label>
                    </div>
                    <div id="entrega_parcelada_campos" class="row mt-2" style="display:none;">
                        <div class="col-md-4">
                            <label class="form-label">Quantidade de parcelas (X)</label>
                            <input type="number" min="1" step="1" name="entrega_parcelas_quantidade" class="form-control form-control-sm" value="{{ old('entrega_parcelas_quantidade') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">1ª parcela em até (Y) dias</label>
                            <input type="number" min="0" step="1" name="entrega_primeira_em_dias" class="form-control form-control-sm" value="{{ old('entrega_primeira_em_dias') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aviso com antecedência de (Z) dias</label>
                            <input type="number" min="0" step="1" name="entrega_aviso_antecedencia_dias" class="form-control form-control-sm" value="{{ old('entrega_aviso_antecedencia_dias') }}">
                        </div>
                    </div>
                    </div>
                </div>

                <!-- 8.2 - Recebimento do bem -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">8.2 Recebimento (ref. 8.2)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Recebimento do bem</label>
                    <div class="row g-2">
                        <div class="col-md-9">
                            <label class="form-label">Endereço completo (com CEP)</label>
                            <textarea name="recebimento_endereco" class="form-control form-control-sm" rows="2">{{ old('recebimento_endereco') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Horário (XXhYYm)</label>
                            <input type="time" name="recebimento_horario" class="form-control form-control-sm" value="{{ old('recebimento_horario') }}">
                        </div>
                    </div>
                    </div>
                </div>

                <!-- 8.3 - Prazo máximo de validade (perecíveis) -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">8.3 Validade Mínima na Entrega (ref. 8.3)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Prazo máximo de validade</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Validade mínima na entrega (X) dias</label>
                            <input type="number" min="0" step="1" name="validade_minima_entrega_dias" class="form-control form-control-sm" value="{{ old('validade_minima_entrega_dias') }}">
                        </div>
                        <div class="col-md-8">
                            <small class="text-muted">No caso de bens perecíveis, o prazo de validade na data da entrega não poderá ser menor que X dias, conforme recomendado pelo fabricante.</small>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary mt-3">
            <div class="card-header bg-white">
            <h5 class="mb-0">9 - Prazo, Forma de Pagamento e Garantia do Contrato</h5>
            </div>

            <div class="card card-outline card-secondary my-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dados Orçamentários da Contratação (ref. 10.1)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Funcional Programática</label>
                            <input type="text" name="funcional_programatica" class="form-control" value="{{ old('funcional_programatica') }}" placeholder="Ex.: 12.364.1234.5678" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Elemento de Despesa</label>
                            <input type="text" name="elemento_despesa" class="form-control" value="{{ old('elemento_despesa') }}" placeholder="Ex.: 3.3.90.30" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fonte do Recurso</label>
                            <input type="text" name="fonte_recurso" class="form-control" value="{{ old('fonte_recurso') }}" placeholder="Ex.: 100 (Recursos Ordinários)" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- 9.1 - Prazo do contrato -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">9.1 Prazo do Contrato (ref. 9.1)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Prazo do contrato</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="prazo_contrato" id="prazo_30_dias" value="30_dias" @checked(old('prazo_contrato')==='30_dias')>
                        <label class="form-check-label" for="prazo_30_dias">30 dias (pronta entrega)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="prazo_contrato" id="prazo_12_meses" value="12_meses" @checked(old('prazo_contrato')==='12_meses')>
                        <label class="form-check-label" for="prazo_12_meses">12 meses</label>
                    </div>
                    </div>
                </div>

                <!-- 9.2 - Possibilidade de prorrogação -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">9.2 Possibilidade de Prorrogação (ref. 9.2)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Haverá possibilidade de prorrogação?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="prorrogacao_possivel" id="prorrogacao_sim" value="1" @checked(old('prorrogacao_possivel')==='1')>
                        <label class="form-check-label" for="prorrogacao_sim">Sim, nas hipóteses do art. 111 da Lei 14.133/21</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="prorrogacao_possivel" id="prorrogacao_nao" value="0" @checked(old('prorrogacao_possivel')==='0')>
                        <label class="form-check-label" for="prorrogacao_nao">Não</label>
                    </div>
                    </div>
                </div>

                <!-- 9.3 - Forma de pagamento -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">9.3 Forma de Pagamento (ref. 9.3)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Forma de pagamento</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Meio</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pagamento_meio" id="pagamento_meio_ordem" value="ordem_bancaria" @checked(old('pagamento_meio','ordem_bancaria')==='ordem_bancaria')>
                                <label class="form-check-label" for="pagamento_meio_ordem">Ordem bancária</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Onde?</label>
                            <input type="text" name="pagamento_onde" class="form-control form-control-sm" placeholder="Conta corrente da contratada no Banco do Estado do Pará" value="{{ old('pagamento_onde') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prazo (dias corridos)</label>
                            <input type="number" min="0" step="1" name="pagamento_prazo_dias" class="form-control form-control-sm" placeholder="Até X dias corridos" value="{{ old('pagamento_prazo_dias') }}">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Prova da regularidade fiscal</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="regularidade_fiscal_prova_tipo" id="regfiscal_sicaf" value="sicaf_ou_cul" @checked(old('regularidade_fiscal_prova_tipo')==='sicaf_ou_cul')>
                            <label class="form-check-label" for="regfiscal_sicaf">Consulta ao SICAF ou Cadastramento Unificado de Licitante</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="regularidade_fiscal_prova_tipo" id="regfiscal_art68" value="art68_documentos" @checked(old('regularidade_fiscal_prova_tipo')==='art68_documentos')>
                            <label class="form-check-label" for="regfiscal_art68">Apresentação dos documentos do art. 68 da Lei 14.133/21</label>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- 9.4 - Garantia do contrato -->
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header bg-white">
<h5 class="mb-0">9.4 Garantia do Contrato (ref. 9.4)</h5>
                    </div>
                    <div class="card-body">
                    <label class="form-label fw-semibold">Qual a garantia do contrato?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="garantia_contrato_tipo" id="garantia_percentual" value="percentual" @checked(old('garantia_contrato_tipo')==='percentual')>
                        <label class="form-check-label" for="garantia_percentual">Percentual do valor inicial do contrato</label>
                    </div>
                    <div id="garantia_contrato_percentual_group" class="row g-2 mt-1" style="display:none;">
                        <div class="col-md-3">
                            <label class="form-label">Percentual (%)</label>
                            <input type="number" min="0" max="100" step="0.01" name="garantia_contrato_percentual" class="form-control form-control-sm" value="{{ old('garantia_contrato_percentual') }}">
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="garantia_contrato_tipo" id="garantia_nao" value="nao_ha" @checked(old('garantia_contrato_tipo')==='nao_ha')>
                        <label class="form-check-label" for="garantia_nao">Não há</label>
                    </div>
                    <div id="garantia_contrato_justificativa_group" class="mt-2" style="display:none;">
                        <label class="form-label">Justificativa</label>
                        <textarea name="garantia_contrato_justificativa" class="form-control form-control-sm" rows="3">{{ old('garantia_contrato_justificativa') }}</textarea>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white text-end">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Salvar Termo</button>
        </div>
    </form>
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection

@push('css')
@endpush

@push('js')
<script>
$(function(){
    function initSummernote() {
        if (!$.fn.summernote) return; // aguarda carregamento
        $('.tr-editor').summernote({
            minHeight: 120,
            maxHeight: 600,
            placeholder: 'Digite seu conteúdo',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onInit: function () {
                    var $editable = $(this).next('.note-editor').find('.note-editable');
                    var minH = 120, maxH = 600;
                    $editable.css({'overflow-y': 'hidden'});
                    var resize = function() {
                        if (!$editable[0]) return;
                        $editable[0].style.height = 'auto';
                        var h = Math.max(minH, Math.min($editable[0].scrollHeight, maxH));
                        $editable[0].style.height = h + 'px';
                    };
                    resize();
                    $editable.on('input', resize);
                },
                onKeyup: function () {
                    var $editable = $(this).next('.note-editor').find('.note-editable');
                    $editable.trigger('input');
                },
                onPaste: function () {
                    var $editable = $(this).next('.note-editor').find('.note-editable');
                    setTimeout(function(){ $editable.trigger('input'); }, 0);
                }
            }
        });
    }

    function ensureSummernoteAssets() {
        if (!document.querySelector('link[href*="summernote"]')) {
            var css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css';
            document.head.appendChild(css);
        }

        if (!$.fn.summernote) {
            var js = document.createElement('script');
            js.src = 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js';
            js.onload = initSummernote;
            document.head.appendChild(js);
        } else {
            initSummernote();
        }
    }

    // ---- Itens dinâmicos (criação) ----
    function parseNumberBR(value) {
        if (!value) return 0;
        let v = ('' + value).replace(/[^0-9,\.\,]/g, '');
        if (v.indexOf(',') !== -1 && v.indexOf('.') !== -1) {
            v = v.replace(/\./g, '').replace(',', '.');
        } else if (v.indexOf(',') !== -1) {
            v = v.replace(',', '.');
        }
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    function formatMoneyBR(n) {
        return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $(document).on('blur', '.input-money', function(){
        const n = parseNumberBR(this.value);
        this.value = formatMoneyBR(n);
    });

    let itemIndex = 0;

    function recalcTotals() {
        let total = 0;
        $('#itens-body tr').each(function(){
            const qtd = parseNumberBR($(this).find('input[name$="[quantidade]"]').val());
            const vu  = parseNumberBR($(this).find('input[name$="[valor_unitario]"]').val());
            const vt  = qtd * vu;
            $(this).find('.col-total').text('R$ ' + formatMoneyBR(vt));
            total += vt;
        });
        $('#total-itens').text('R$ ' + formatMoneyBR(total));
        $('#itens-body tr').each(function(i){ $(this).find('.col-idx').text(i+1); });
        // Atualiza o campo Valor Estimado com a soma dos itens (se não estiver em override)
        if (!$('#estimado_override').is(':checked')) {
            $('input[name="valor_estimado"]').val(formatMoneyBR(total));
        }
        checkEstimadoDiff();
    }

    function getTotalItens(){
        let total = 0;
        $('#itens-body tr').each(function(){
            const qtd = parseNumberBR($(this).find('input[name$="[quantidade]"]').val());
            const vu  = parseNumberBR($(this).find('input[name$="[valor_unitario]"]').val());
            total += (qtd * vu);
        });
        return total;
    }

    function checkEstimadoDiff(){
        const total = getTotalItens();
        const estimado = parseNumberBR($('input[name="valor_estimado"]').val());
        const diff = Math.abs((estimado ?? 0) - total);
        const hasDiff = diff > 0.005; // tolerância de centavos
        const override = $('#estimado_override').is(':checked');
        $('#estimado_diff_alert').toggleClass('d-none', !(override && hasDiff));
    }

    $(document).on('change', '#estimado_override', function(){
        const override = $(this).is(':checked');
        const $inp = $('input[name="valor_estimado"]');
        $inp.prop('readonly', !override);
        if (!override) {
            $inp.val(formatMoneyBR(getTotalItens()));
        }
        checkEstimadoDiff();
    });

    $(document).on('blur', 'input[name="valor_estimado"]', function(){
        if (!$('#estimado_override').is(':checked')) return;
        const n = parseNumberBR(this.value);
        this.value = formatMoneyBR(n);
        checkEstimadoDiff();
    });

    // Validação no submit: números válidos e campos obrigatórios
    $(document).on('submit', 'form[action*="termos-referencia.store"]', function(e){
        let errors = [];
        // Valida itens
        $('#itens-body tr').each(function(idx){
            const $qtd = $(this).find('input[name$="[quantidade]"]');
            const $vu  = $(this).find('input[name$="[valor_unitario]"]');
            const qtd = parseNumberBR($qtd.val());
            const vu  = parseNumberBR($vu.val());
            $qtd.toggleClass('is-invalid', !(qtd > 0));
            $vu.toggleClass('is-invalid', !(vu >= 0));
            if (!(qtd > 0)) { errors.push(`Item ${idx+1}: quantidade inválida`); }
            if (isNaN(vu)) { errors.push(`Item ${idx+1}: valor unitário inválido`); }
        });
        // Se override e diferença, alerta de confirmação
        const override = $('#estimado_override').is(':checked');
        const total = getTotalItens();
        const estimado = parseNumberBR($('input[name="valor_estimado"]').val());
        const diff = Math.abs((estimado ?? 0) - total);
        if (override && diff > 0.005) {
            const ok = confirm('O Valor Estimado difere do total dos itens. Deseja prosseguir mesmo assim?');
            if (!ok) {
                e.preventDefault();
                return false;
            }
        }
        if (errors.length) {
            e.preventDefault();
            alert('Corrija os seguintes problemas antes de salvar:\n\n' + errors.join('\n'));
            return false;
        }
        return true;
    });

    function newItemRow() {
        const idx = itemIndex++;
        const row = $(
            '<tr>'+
              '<td class="col-idx"></td>'+
              '<td><input type="text" name="itens['+idx+'][descricao]" class="form-control form-control-sm" required></td>'+
              '<td><input type="text" name="itens['+idx+'][unidade]" class="form-control form-control-sm" placeholder="un, kg"></td>'+
              '<td class="text-end"><input type="text" name="itens['+idx+'][quantidade]" class="form-control form-control-sm input-number" required></td>'+
              '<td class="text-end"><div class="input-group input-group-sm"><span class="input-group-text">R$</span><input type="text" name="itens['+idx+'][valor_unitario]" class="form-control form-control-sm input-money" required></div></td>'+
              '<td class="text-end col-total">R$ 0,00</td>'+
              '<td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm btn-remove-item"><i class="fas fa-trash"></i></button></td>'+
            '</tr>'
        );
        $('#itens-body').append(row);
        recalcTotals();
    }

    $('#btn-add-item').on('click', function(){ newItemRow(); });
    $('#btn-clear-items').on('click', function(){ $('#itens-body').empty(); recalcTotals(); });
    $(document).on('blur', '.input-number, .input-money', recalcTotals);
    $(document).on('click', '.btn-remove-item', function(){ $(this).closest('tr').remove(); recalcTotals(); });

    ensureSummernoteAssets();

    // Toggle de campos de bens conforme tipo de TR
    function toggleBensCampos(){
        var tipo = $('select[name="tipo_tr"]').val();
        if (tipo === 'bens_comuns') {
            $('#bens_campos').show();
            $('#bens_condicoes_avancadas').show();
        } else {
            $('#bens_campos').hide();
            $('#bens_condicoes_avancadas').hide();
        }
    }
    $('select[name="tipo_tr"]').on('change', toggleBensCampos);
    toggleBensCampos();

    // Toggle justificativa da prova de qualidade
    function toggleProvaQualidadeJustificativa(){
        var val = $('input[name="prova_qualidade"]:checked').val();
        if (val === '1') {
            $('#prova_qualidade_justificativa_group').show();
        } else {
            $('#prova_qualidade_justificativa_group').hide();
        }
    }
    $(document).on('change', 'input[name="prova_qualidade"]', toggleProvaQualidadeJustificativa);
    toggleProvaQualidadeJustificativa();

    // Toggle justificativa da amostra
    function toggleAmostraJustificativa(){
        var val = $('input[name="edital_exigira_amostra"]:checked').val();
        if (val === '1') {
            $('#edital_amostra_justificativa_group').show();
        } else {
            $('#edital_amostra_justificativa_group').hide();
        }
    }
    $(document).on('change', 'input[name="edital_exigira_amostra"]', toggleAmostraJustificativa);
    toggleAmostraJustificativa();

    // Toggle campos de garantia do bem
    function toggleGarantiaBemCampos(){
        var val = $('input[name="garantia_bem"]:checked').val();
        if (val === '1') {
            $('#garantia_bem_campos').show();
        } else {
            $('#garantia_bem_campos').hide();
        }
    }
    $(document).on('change', 'input[name="garantia_bem"]', toggleGarantiaBemCampos);
    toggleGarantiaBemCampos();

    // Toggle meses de assistência técnica
    function toggleAssistenciaMeses(){
        var tipo = $('input[name="assistencia_tecnica_tipo"]:checked').val();
        if (tipo === 'credenciada' || tipo === 'propria') {
            $('#assistencia_tecnica_meses_group').show();
        } else {
            $('#assistencia_tecnica_meses_group').hide();
        }
    }
    $(document).on('change', 'input[name="assistencia_tecnica_tipo"]', toggleAssistenciaMeses);
    toggleAssistenciaMeses();

    // Toggle justificativa de orçamento sigiloso
    function toggleOrcamentoSigilosoJustificativa(){
        var val = $('input[name="orcamento_sigiloso"]:checked').val();
        if (val === '1') {
            $('#orcamento_sigiloso_justificativa_group').show();
        } else {
            $('#orcamento_sigiloso_justificativa_group').hide();
        }
    }
    $(document).on('change', 'input[name="orcamento_sigiloso"]', toggleOrcamentoSigilosoJustificativa);
    toggleOrcamentoSigilosoJustificativa();

    // Toggle itens exclusivos ME/EPP
    function toggleItensExclusivosLista(){
        var val = $('input[name="itens_exclusivos_me_epp"]:checked').val();
        if (val === '1') {
            $('#itens_exclusivos_lista_group').show();
        } else {
            $('#itens_exclusivos_lista_group').hide();
        }
    }
    $(document).on('change', 'input[name="itens_exclusivos_me_epp"]', toggleItensExclusivosLista);
    toggleItensExclusivosLista();

    // 7.2 Habilitação técnica campos
    function toggleHabilitacaoTecnica(){
        var val = $('input[name="habilitacao_tecnica_exigida"]:checked').val();
        if (val === '1') {
            $('#habilitacao_tecnica_campos').show();
        } else {
            $('#habilitacao_tecnica_campos').hide();
        }
    }
    $(document).on('change', 'input[name="habilitacao_tecnica_exigida"]', toggleHabilitacaoTecnica);
    toggleHabilitacaoTecnica();

    // 7.3 Justificativas por checkbox
    function bindToggleCheckboxJustif(cbSelector, groupSelector){
        var checked = $(cbSelector).is(':checked');
        $(groupSelector).toggle(checked);
    }
    function refreshQualificacoes(){
        bindToggleCheckboxJustif('#qt_declaracao_ciencia', '#qt_declaracao_justificativa_group');
        bindToggleCheckboxJustif('#qt_registro_entidade', '#qt_registro_justificativa_group');
        bindToggleCheckboxJustif('#qt_indicacao_pessoal', '#qt_indicacao_justificativa_group');
        var outro = $('#qt_outro').is(':checked');
        $('#qt_outro_campos').toggle(outro);
        var nao = $('#qt_nao_exigida').is(':checked');
        // Se marcar "não exigida", desabilita demais opções
        $('#qt_declaracao_ciencia, #qt_registro_entidade, #qt_indicacao_pessoal, #qt_outro').prop('disabled', nao);
        if (nao) {
            $('#qt_declaracao_ciencia, #qt_registro_entidade, #qt_indicacao_pessoal, #qt_outro').prop('checked', false);
            $('#qt_declaracao_justificativa_group, #qt_registro_justificativa_group, #qt_indicacao_justificativa_group, #qt_outro_campos').hide();
        }
    }
    $(document).on('change', '#qt_declaracao_ciencia, #qt_registro_entidade, #qt_indicacao_pessoal, #qt_outro, #qt_nao_exigida', refreshQualificacoes);
    refreshQualificacoes();

    // 7.4 Sustentabilidade
    function toggleSustentabilidade(){
        var val = $('input[name="criterio_sustentabilidade"]:checked').val();
        $('#criterio_sustentabilidade_especificar_group').toggle(val === '1');
    }
    $(document).on('change', 'input[name="criterio_sustentabilidade"]', toggleSustentabilidade);
    toggleSustentabilidade();

    // 7.5 Riscos assumidos
    function toggleRiscos(){
        var val = $('input[name="riscos_assumidos_contratada"]:checked').val();
        $('#riscos_assumidos_especificar_group').toggle(val === '1');
    }
    $(document).on('change', 'input[name="riscos_assumidos_contratada"]', toggleRiscos);
    toggleRiscos();
    
    // 8.1 Entrega forma - mostrar campos de parcelamento
    function toggleEntregaForma(){
        var val = $('input[name="entrega_forma"]:checked').val();
        $('#entrega_parcelada_campos').toggle(val === 'parcelada');
    }
    $(document).on('change', 'input[name="entrega_forma"]', toggleEntregaForma);
    toggleEntregaForma();

    // 9.4 Garantia do contrato - campos condicionais
    function toggleGarantiaContratoCampos(){
        var val = $('input[name="garantia_contrato_tipo"]:checked').val();
        if (val === 'percentual') {
            $('#garantia_contrato_percentual_group').show();
            $('#garantia_contrato_justificativa_group').show();
        } else if (val === 'nao_ha') {
            $('#garantia_contrato_percentual_group').hide();
            $('#garantia_contrato_justificativa_group').show();
        } else {
            $('#garantia_contrato_percentual_group').hide();
            $('#garantia_contrato_justificativa_group').hide();
        }
    }
    $(document).on('change', 'input[name="garantia_contrato_tipo"]', toggleGarantiaContratoCampos);
    toggleGarantiaContratoCampos();
});
</script>
@endpush