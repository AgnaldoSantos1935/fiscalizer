<?php

namespace App\Http\Controllers;

use App\Models\TermoReferencia;
use App\Models\TermoReferenciaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;

class TermoReferenciaController extends Controller
{
    private function normalizeNumber($value)
    {
        if ($value === null) return null;
        $s = (string) $value;
        $s = str_replace(['.', ','], ['', '.'], $s);
        return is_numeric($s) ? (float)$s : null;
    }

    public function index()
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('termos_referencia')) {
            $trs = TermoReferencia::latest()->paginate(10);
        } else {
            $trs = collect();
        }
        return view('termos_referencia.index', compact('trs'));
    }

    public function getJson()
    {
        $data = TermoReferencia::select('id','titulo','status','valor_estimado','created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($tr){
                return [
                    'id' => $tr->id,
                    'titulo' => $tr->titulo,
                    'status' => $tr->status,
                    'valor_estimado' => $tr->valor_estimado,
                    'show_url' => route('contratacoes.termos-referencia.show', $tr),
                    'edit_url' => route('contratacoes.termos-referencia.edit', $tr),
                    'pdf_url' => route('contratacoes.termos-referencia.pdf', $tr),
                ];
            });
        return response()->json(['data' => $data]);
    }

    public function create()
    {
        return view('termos_referencia.create');
    }

    public function store(Request $request)
    {
        // Normaliza valor estimado com máscara pt-BR
        $request->merge([
            'valor_estimado' => $this->normalizeNumber($request->input('valor_estimado')),
        ]);
        $rules = [
            'titulo' => 'required|string|max:255',
            'tipo_tr' => 'nullable|in:bens_comuns,servicos_sem_mao_de_obra_sem_prorrogacao',
            'pae_numero' => 'nullable|string|max:50',
            'cidade' => 'nullable|string|max:100',
            'data_emissao' => 'nullable|date',
            'responsavel_nome' => 'nullable|string|max:150',
            'responsavel_cargo' => 'nullable|string|max:150',
            'responsavel_matricula' => 'nullable|string|max:50',
            'objeto' => 'nullable|string',
            'justificativa' => 'nullable|string',
            'escopo' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'criterios_julgamento' => 'nullable|string',
            'prazos' => 'nullable|string',
            'local_execucao' => 'nullable|string',
            'forma_pagamento' => 'nullable|string',
            // novos campos
            'modelo_execucao' => 'nullable|string',
            'modelo_gestao' => 'nullable|string',
            'criterios_medicao_pagamento' => 'nullable|string',
            'forma_criterios_selecao_fornecedor' => 'nullable|string',
            // 5.1 Prova de qualidade
            'prova_qualidade' => 'nullable|boolean',
            'prova_qualidade_justificativa' => 'nullable|string',
            // 5.2 Amostra
            'edital_exigira_amostra' => 'nullable|boolean',
            'edital_amostra_justificativa' => 'nullable|string',
            // 5.3 Garantia do bem (bens)
            'garantia_bem' => 'nullable|boolean',
            'garantia_bem_itens' => 'nullable|string',
            'garantia_bem_meses' => 'nullable|integer|min:0',
            // 5.4 Assistência técnica (bens)
            'assistencia_tecnica_tipo' => 'nullable|in:credenciada,propria,nao',
            'assistencia_tecnica_meses' => 'nullable|integer|min:0',
            // 6.1 Forma de contratação
            'forma_contratacao' => 'nullable|in:inexigibilidade_art74_y,dispensa_valor_art75_ii,dispensa_art75_y,pregao_eletronico,concorrencia',
            // 6.2 Critério de julgamento (seleção)
            'criterio_julgamento_tipo' => 'nullable|in:menor_preco,maior_desconto',
            // 6.3 Orçamento sigiloso
            'orcamento_sigiloso' => 'nullable|boolean',
            'orcamento_sigiloso_justificativa' => 'nullable|string',
            // 6.5 Itens exclusivos ME/EPP
            'itens_exclusivos_me_epp' => 'nullable|boolean',
            'itens_exclusivos_lista' => 'nullable|string',
            // 7.1 Habilitação jurídica
            'habilitacao_juridica_existencia' => 'nullable|boolean',
            'habilitacao_juridica_autorizacao' => 'nullable|boolean',
            // 7.2 Habilitação técnica
            'habilitacao_tecnica_exigida' => 'nullable|boolean',
            'habilitacao_tecnica_qual' => 'nullable|string',
            'habilitacao_tecnica_justificativa' => 'nullable|string',
            // 7.3 Qualificações técnicas exigidas
            'qt_declaracao_ciencia' => 'nullable|boolean',
            'qt_declaracao_justificativa' => 'nullable|string',
            'qt_registro_entidade' => 'nullable|boolean',
            'qt_registro_justificativa' => 'nullable|string',
            'qt_indicacao_pessoal' => 'nullable|boolean',
            'qt_indicacao_justificativa' => 'nullable|string',
            'qt_outro' => 'nullable|boolean',
            'qt_outro_especificar' => 'nullable|string',
            'qt_outro_justificativa' => 'nullable|string',
            'qt_nao_exigida' => 'nullable|boolean',
            // 7.4 Sustentabilidade
            'criterio_sustentabilidade' => 'nullable|boolean',
            'criterio_sustentabilidade_especificar' => 'nullable|string',
            // 7.5 Riscos assumidos pela contratada
            'riscos_assumidos_contratada' => 'nullable|boolean',
            'riscos_assumidos_especificar' => 'nullable|string',
            'especificacao_produto' => 'nullable|string',
            'locais_entrega_recebimento' => 'nullable|string',
            // 8.x Entrega e recebimento
            'entrega_forma' => 'nullable|in:total,parcelada',
            'entrega_parcelas_quantidade' => 'nullable|integer|min:1',
            'entrega_primeira_em_dias' => 'nullable|integer|min:0',
            'entrega_aviso_antecedencia_dias' => 'nullable|integer|min:0',
            'recebimento_endereco' => 'nullable|string',
            'recebimento_horario' => 'nullable|string|max:10',
            'validade_minima_entrega_dias' => 'nullable|integer|min:0',
            'garantia_manutencao_assistencia' => 'nullable|string',
            // flags Sim/Não
            'garantia_exigida' => 'nullable|boolean',
            'manutencao_incluida' => 'nullable|boolean',
            'assistencia_tecnica_incluida' => 'nullable|boolean',
            'adequacao_orcamentaria_confirmada' => 'nullable|boolean',
            'estimativas_valor_texto' => 'nullable|string',
            'adequacao_orcamentaria' => 'nullable|string',
            // 9.x Prazo, pagamento e garantia do contrato
            'prazo_contrato' => 'nullable|in:30_dias,12_meses',
            'prorrogacao_possivel' => 'nullable|boolean',
            'pagamento_meio' => 'nullable|in:ordem_bancaria',
            'pagamento_onde' => 'nullable|string|max:255',
            'pagamento_prazo_dias' => 'nullable|integer|min:0',
            'regularidade_fiscal_prova_tipo' => 'nullable|in:sicaf_ou_cul,art68_documentos',
            'garantia_contrato_tipo' => 'nullable|in:percentual,nao_ha',
            'garantia_contrato_percentual' => 'nullable|numeric|min:0|max:100',
            'garantia_contrato_justificativa' => 'nullable|string',
            'valor_estimado' => 'nullable|numeric',
            // 10.x Dados orçamentários
            'funcional_programatica' => 'nullable|string|max:100',
            'elemento_despesa' => 'nullable|string|max:50',
            'fonte_recurso' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function($v) use ($request){
            foreach ([
                'objeto','justificativa','escopo','requisitos','criterios_julgamento','prazos','local_execucao','forma_pagamento',
                'modelo_execucao','modelo_gestao','criterios_medicao_pagamento','forma_criterios_selecao_fornecedor',
                'prova_qualidade_justificativa','edital_amostra_justificativa','orcamento_sigiloso_justificativa',
                'itens_exclusivos_lista','garantia_bem_itens',
                'habilitacao_tecnica_qual','habilitacao_tecnica_justificativa',
                'qt_declaracao_justificativa','qt_registro_justificativa','qt_indicacao_justificativa',
                'qt_outro_especificar','qt_outro_justificativa',
                'criterio_sustentabilidade_especificar','riscos_assumidos_especificar',
                'especificacao_produto','locais_entrega_recebimento','garantia_manutencao_assistencia',
                'recebimento_endereco','garantia_contrato_justificativa',
                'funcional_programatica','elemento_despesa','fonte_recurso',
                'estimativas_valor_texto','adequacao_orcamentaria'
            ] as $field) {
                $content = (string) $request->input($field);
                if ($content !== '') {
                    $lines = $this->countLines($content);
                    if ($lines > 200) {
                        $v->errors()->add($field, 'Limite máximo de 200 linhas por campo.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        // Cidade: prioriza Pessoa ligada ao usuário; fallback para Belém
        $user = \Illuminate\Support\Facades\Auth::user();
        $pessoa = optional($user)->pessoa;
        if (!empty($pessoa?->cidade)) {
            $validated['cidade'] = $pessoa->cidade;
        }
        if (!isset($validated['cidade']) || trim((string)$validated['cidade']) === '') {
            $validated['cidade'] = 'Belém';
        }

        // Força campos de responsável a virem da sessão do usuário
        $user = \Illuminate\Support\Facades\Auth::user();
        $profile = \App\Models\UserProfile::where('user_id', $user?->id)->first();
        $validated['responsavel_nome'] = $profile->nome_completo ?? $user?->name ?? ($validated['responsavel_nome'] ?? null);
        $validated['responsavel_cargo'] = $profile->cargo ?? (optional($user?->role)->nome ?? ($validated['responsavel_cargo'] ?? null));
        $validated['responsavel_matricula'] = $profile->matricula ?? ($validated['responsavel_matricula'] ?? null);

        $tr = TermoReferencia::create($validated);

        // Persiste itens dinâmicos, se enviados
        $itens = $request->input('itens', []);
        foreach ($itens as $i) {
            $descricao = trim((string)($i['descricao'] ?? ''));
            if ($descricao === '') { continue; }
            $unidade = $i['unidade'] ?? null;
            $quantidade = $this->normalizeNumber($i['quantidade'] ?? 0);
            $valorUnit  = $this->normalizeNumber($i['valor_unitario'] ?? 0);
            if (($quantidade ?? 0) <= 0 && ($valorUnit ?? 0) <= 0) { continue; }
            $tr->itens()->create([
                'descricao'      => $descricao,
                'unidade'        => $unidade,
                'quantidade'     => $quantidade ?? 0,
                'valor_unitario' => $valorUnit ?? 0,
            ]);
        }

        return redirect()->route('contratacoes.termos-referencia.show', $tr)->with('success', 'Termo de Referência criado com sucesso!');
    }

    public function show(TermoReferencia $tr)
    {
        $tr->loadMissing(['itens', 'logs.usuario']);
        return view('termos_referencia.show', compact('tr'));
    }

    public function edit(TermoReferencia $tr)
    {
        if ($tr->status === 'finalizado') {
            return redirect()->route('contratacoes.termos-referencia.show', $tr)
                ->with('warning', 'Edição desabilitada: TR está finalizado.');
        }
        return view('termos_referencia.edit', compact('tr'));
    }

    public function update(Request $request, TermoReferencia $tr)
    {
        if ($tr->status === 'finalizado') {
            return redirect()->route('contratacoes.termos-referencia.show', $tr)
                ->with('error', 'Não é possível editar um TR finalizado.');
        }
        $rules = [
            'titulo' => 'required|string|max:255',
            'tipo_tr' => 'nullable|in:bens_comuns,servicos_sem_mao_de_obra_sem_prorrogacao',
            'pae_numero' => 'nullable|string|max:50',
            'cidade' => 'nullable|string|max:100',
            'data_emissao' => 'nullable|date',
            'responsavel_nome' => 'nullable|string|max:150',
            'responsavel_cargo' => 'nullable|string|max:150',
            'responsavel_matricula' => 'nullable|string|max:50',
            'objeto' => 'nullable|string',
            'justificativa' => 'nullable|string',
            'escopo' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'criterios_julgamento' => 'nullable|string',
            'prazos' => 'nullable|string',
            'local_execucao' => 'nullable|string',
            'forma_pagamento' => 'nullable|string',
            'valor_estimado' => 'nullable|numeric',
            // flags Sim/Não
            'garantia_exigida' => 'nullable|boolean',
            'manutencao_incluida' => 'nullable|boolean',
            'assistencia_tecnica_incluida' => 'nullable|boolean',
            'adequacao_orcamentaria_confirmada' => 'nullable|boolean',
            // 5.1 Prova de qualidade
            'prova_qualidade' => 'nullable|boolean',
            'prova_qualidade_justificativa' => 'nullable|string',
            // 5.2 Amostra
            'edital_exigira_amostra' => 'nullable|boolean',
            'edital_amostra_justificativa' => 'nullable|string',
            // 5.3 Garantia do bem (bens)
            'garantia_bem' => 'nullable|boolean',
            'garantia_bem_itens' => 'nullable|string',
            'garantia_bem_meses' => 'nullable|integer|min:0',
            // 5.4 Assistência técnica (bens)
            'assistencia_tecnica_tipo' => 'nullable|in:credenciada,propria,nao',
            'assistencia_tecnica_meses' => 'nullable|integer|min:0',
            // 6.1 Forma de contratação
            'forma_contratacao' => 'nullable|in:inexigibilidade_art74_y,dispensa_valor_art75_ii,dispensa_art75_y,pregao_eletronico,concorrencia',
            // 6.2 Critério de julgamento (seleção)
            'criterio_julgamento_tipo' => 'nullable|in:menor_preco,maior_desconto',
            // 6.3 Orçamento sigiloso
            'orcamento_sigiloso' => 'nullable|boolean',
            'orcamento_sigiloso_justificativa' => 'nullable|string',
            // 6.5 Itens exclusivos ME/EPP
            'itens_exclusivos_me_epp' => 'nullable|boolean',
            'itens_exclusivos_lista' => 'nullable|string',
            // 7.1 Habilitação jurídica
            'habilitacao_juridica_existencia' => 'nullable|boolean',
            'habilitacao_juridica_autorizacao' => 'nullable|boolean',
            // 7.2 Habilitação técnica
            'habilitacao_tecnica_exigida' => 'nullable|boolean',
            'habilitacao_tecnica_qual' => 'nullable|string',
            'habilitacao_tecnica_justificativa' => 'nullable|string',
            // 7.3 Qualificações técnicas exigidas
            'qt_declaracao_ciencia' => 'nullable|boolean',
            'qt_declaracao_justificativa' => 'nullable|string',
            'qt_registro_entidade' => 'nullable|boolean',
            'qt_registro_justificativa' => 'nullable|string',
            'qt_indicacao_pessoal' => 'nullable|boolean',
            'qt_indicacao_justificativa' => 'nullable|string',
            'qt_outro' => 'nullable|boolean',
            'qt_outro_especificar' => 'nullable|string',
            'qt_outro_justificativa' => 'nullable|string',
            'qt_nao_exigida' => 'nullable|boolean',
            // 7.4 Sustentabilidade
            'criterio_sustentabilidade' => 'nullable|boolean',
            'criterio_sustentabilidade_especificar' => 'nullable|string',
            // 7.5 Riscos assumidos pela contratada
            'riscos_assumidos_contratada' => 'nullable|boolean',
            'riscos_assumidos_especificar' => 'nullable|string',
            // 8.x Entrega e recebimento
            'entrega_forma' => 'nullable|in:total,parcelada',
            'entrega_parcelas_quantidade' => 'nullable|integer|min:1',
            'entrega_primeira_em_dias' => 'nullable|integer|min:0',
            'entrega_aviso_antecedencia_dias' => 'nullable|integer|min:0',
            'recebimento_endereco' => 'nullable|string',
            'recebimento_horario' => 'nullable|string|max:10',
            'validade_minima_entrega_dias' => 'nullable|integer|min:0',
            // 9.x Prazo, pagamento e garantia do contrato
            'prazo_contrato' => 'nullable|in:30_dias,12_meses',
            'prorrogacao_possivel' => 'nullable|boolean',
            'pagamento_meio' => 'nullable|in:ordem_bancaria',
            'pagamento_onde' => 'nullable|string|max:255',
            'pagamento_prazo_dias' => 'nullable|integer|min:0',
            'regularidade_fiscal_prova_tipo' => 'nullable|in:sicaf_ou_cul,art68_documentos',
            'garantia_contrato_tipo' => 'nullable|in:percentual,nao_ha',
            'garantia_contrato_percentual' => 'nullable|numeric|min:0|max:100',
            'garantia_contrato_justificativa' => 'nullable|string',
            // 10.x Dados orçamentários
            'funcional_programatica' => 'nullable|string|max:100',
            'elemento_despesa' => 'nullable|string|max:50',
            'fonte_recurso' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function($v) use ($request){
            foreach ([
                'objeto','justificativa','escopo','requisitos','criterios_julgamento','prazos','local_execucao','forma_pagamento',
                'prova_qualidade_justificativa','edital_amostra_justificativa','orcamento_sigiloso_justificativa',
                'itens_exclusivos_lista','garantia_bem_itens',
                'habilitacao_tecnica_qual','habilitacao_tecnica_justificativa',
                'qt_declaracao_justificativa','qt_registro_justificativa','qt_indicacao_justificativa',
                'qt_outro_especificar','qt_outro_justificativa',
                'criterio_sustentabilidade_especificar','riscos_assumidos_especificar',
                'recebimento_endereco','garantia_contrato_justificativa',
                'funcional_programatica','elemento_despesa','fonte_recurso'
            ] as $field) {
                $content = (string) $request->input($field);
                if ($content !== '') {
                    $lines = $this->countLines($content);
                    if ($lines > 200) {
                        $v->errors()->add($field, 'Limite máximo de 200 linhas por campo.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        // Cidade: prioriza Pessoa ligada ao usuário; fallback para Belém
        $user = \Illuminate\Support\Facades\Auth::user();
        $pessoa = optional($user)->pessoa;
        if (!empty($pessoa?->cidade)) {
            $validated['cidade'] = $pessoa->cidade;
        }
        if (!isset($validated['cidade']) || trim((string)$validated['cidade']) === '') {
            $validated['cidade'] = 'Belém';
        }

        // Força atualização dos campos de responsável pela sessão do usuário
        $user = \Illuminate\Support\Facades\Auth::user();
        $profile = \App\Models\UserProfile::where('user_id', $user?->id)->first();
        $validated['responsavel_nome'] = $profile->nome_completo ?? $user?->name ?? ($validated['responsavel_nome'] ?? null);
        $validated['responsavel_cargo'] = $profile->cargo ?? (optional($user?->role)->nome ?? ($validated['responsavel_cargo'] ?? null));
        $validated['responsavel_matricula'] = $profile->matricula ?? ($validated['responsavel_matricula'] ?? null);

        $tr->update($validated);
        return redirect()->route('contratacoes.termos-referencia.show', $tr)->with('success', 'Termo de Referência atualizado com sucesso!');
    }

    /**
     * Workflow: Elaborador envia para aprovação (status: em_analise)
     */
    public function enviarAprovacao(TermoReferencia $tr, Request $request)
    {
        if ($tr->status !== 'em_analise') {
            $tr->update(['status' => 'em_analise']);
        }
        TermoReferenciaLog::create([
            'termo_referencia_id' => $tr->id,
            'acao' => 'enviar_aprovacao',
            'usuario_id' => \Illuminate\Support\Facades\Auth::id(),
            'motivo' => $request->input('motivo')
        ]);
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência enviado para aprovação.');
    }

    /**
     * Workflow: Gestor aprova (status: finalizado)
     */
    public function aprovar(TermoReferencia $tr, Request $request)
    {
        // Valida transições antes da aprovação
        $erros = $this->validarAprovacao($tr);
        if (count($erros) > 0) {
            return redirect()->route('contratacoes.termos-referencia.show', $tr)
                ->with('error', 'Não foi possível aprovar o TR. Corrija as inconsistências.')
                ->withErrors(['aprovacao' => $erros]);
        }

        if ($tr->status !== 'finalizado') {
            $tr->update(['status' => 'finalizado']);
        }
        TermoReferenciaLog::create([
            'termo_referencia_id' => $tr->id,
            'acao' => 'aprovar',
            'usuario_id' => \Illuminate\Support\Facades\Auth::id(),
            'motivo' => $request->input('motivo')
        ]);
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência aprovado com sucesso.');
    }

    /**
     * Workflow: Gestor retorna para elaboração (status: rascunho)
     */
    public function retornarElaboracao(TermoReferencia $tr, Request $request)
    {
        if ($tr->status !== 'rascunho') {
            $tr->update(['status' => 'rascunho']);
        }
        TermoReferenciaLog::create([
            'termo_referencia_id' => $tr->id,
            'acao' => 'retornar',
            'usuario_id' => \Illuminate\Support\Facades\Auth::id(),
            'motivo' => $request->input('motivo')
        ]);
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência retornado para elaboração.');
    }

    /**
     * Workflow: Gestor reprova (volta para rascunho com motivo)
     */
    public function reprovar(TermoReferencia $tr, Request $request)
    {
        $data = $request->validate([
            'motivo' => 'required|string|min:3'
        ]);
        if ($tr->status !== 'rascunho') {
            $tr->update(['status' => 'rascunho']);
        }
        TermoReferenciaLog::create([
            'termo_referencia_id' => $tr->id,
            'acao' => 'reprovar',
            'usuario_id' => \Illuminate\Support\Facades\Auth::id(),
            'motivo' => $data['motivo']
        ]);
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência reprovado e retornado para elaboração.');
    }

    private function validarAprovacao(TermoReferencia $tr): array
    {
        $erros = [];
        // Exigir ao menos 1 item
        $count = $tr->itens()->count();
        if ($count === 0) {
            $erros[] = 'TR não possui itens cadastrados.';
        }
        // Itens com quantidade e valor válidos
        $invalidItens = $tr->itens()
            ->where(function($q){
                $q->where('quantidade', '<=', 0)->orWhere('valor_unitario', '<=', 0);
            })->count();
        if ($invalidItens > 0) {
            $erros[] = 'Existem itens com quantidade ou valor unitário inválidos.';
        }
        // Valor estimado positivo
        if (($tr->valor_estimado ?? 0) <= 0) {
            $erros[] = 'Valor estimado deve ser informado e maior que zero.';
        }
        // Justificativas obrigatórias quando sinalizadores ativos
        if ($tr->orcamento_sigiloso && empty($tr->orcamento_sigiloso_justificativa)) {
            $erros[] = 'Orçamento sigiloso selecionado requer justificativa.';
        }
        if ($tr->itens_exclusivos_me_epp && empty($tr->itens_exclusivos_lista)) {
            $erros[] = 'Itens exclusivos ME/EPP selecionados requerem lista especificada.';
        }
        // Campos essenciais
        if (empty($tr->titulo) || empty($tr->objeto)) {
            $erros[] = 'Título e objeto do TR são obrigatórios.';
        }
        return $erros;
    }

    public function destroy(TermoReferencia $tr)
    {
        $tr->delete();
        return redirect()->route('contratacoes.termos-referencia.index')->with('success', 'Termo de Referência removido com sucesso!');
    }

    private function countLines(string $content): int
    {
        // Normaliza quebras de linha a partir de HTML do editor
        $normalized = preg_replace('/<br\s*\/?>/i', "\n", $content);
        $normalized = preg_replace('/<\/(p|div|li)>/i', "\n", $normalized);
        $plain = strip_tags($normalized);
        $plain = trim($plain);
        if ($plain === '') return 0;
        $lines = preg_split('/\r\n|\r|\n/', $plain);
        return count($lines);
    }

    public function pdf(TermoReferencia $tr)
    {
        // Renderiza PDF a partir de Blade
        $pdf = PDF::loadView('termos_referencia.pdf', [
            'tr' => $tr,
        ])->setPaper('a4');

        // Salva no disco público
        $filename = 'tr_pdfs/TR_'.$tr->id.'_'.now()->format('Ymd_His').'.pdf';
        $path = storage_path('app/public/'.$filename);
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $pdf->output());

        // Registra documento para visualização
        $doc = \App\Models\Documento::create([
            'tipo'           => 'tr_pdf',
            'titulo'         => $tr->titulo,
            'descricao'      => 'PDF gerado do Termo de Referência #'.$tr->id,
            'caminho_arquivo'=> 'tr_pdfs/'.basename($path),
            'data_upload'    => now(),
            'metadados'      => [
                'tr_id' => $tr->id,
                'tipo_tr' => $tr->tipo_tr,
            ],
            'created_by'     => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('documentos.visualizar', [
            'documento' => $doc->id,
            'return_to' => route('contratacoes.termos-referencia.show', $tr),
        ]);
    }
}
