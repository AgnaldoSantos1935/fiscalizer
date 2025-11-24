<?php

namespace App\Http\Controllers;

use App\Models\TermoReferencia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TermoReferenciaController extends Controller
{
    public function index()
    {
        return view('termos_referencia.index');
    }

    public function create()
    {
        return view('termos_referencia.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'tipo_tr' => 'required|string|max:100',
            'objeto' => 'nullable|string',
            'justificativa' => 'nullable|string',
            'escopo' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'criterios_julgamento' => 'nullable|string',
            'forma_criterios_selecao_fornecedor' => 'nullable|string',
            'criterios_medicao_pagamento' => 'nullable|string',
            'fundamentacao_legal_texto' => 'nullable|string',
            'habilitacao_tecnica_exigida' => 'nullable|boolean',
            'habilitacao_tecnica_qual' => 'nullable|string',
            'habilitacao_tecnica_justificativa' => 'nullable|string',
            'habilitacao_tecnica_percentual_minimo' => 'nullable|numeric|min:0|max:100',
            'habilitacao_tecnica_documentos' => 'nullable|string',
            'criterio_sustentabilidade' => 'nullable|boolean',
            'criterio_sustentabilidade_especificar' => 'nullable|string',
            'garantia_bem_meses' => 'nullable|integer|min:0',
            'assistencia_tecnica_meses' => 'nullable|integer|min:0',
            'pagamento_prazo_dias' => 'nullable|integer|min:0',
            'garantia_contrato_tipo' => 'nullable|string',
            'garantia_contrato_percentual' => 'nullable|numeric|min:0|max:100',
            'garantia_contrato_justificativa' => 'nullable|string',
            'orcamento_sigiloso' => 'nullable|boolean',
            'forma_contratacao' => 'nullable|string|max:100',
            'criterio_julgamento_tipo' => 'nullable|string|max:100',
            'subcontratacao_permitida' => 'nullable|boolean',
            'subcontratacao_excecao' => 'nullable|string',
            'penalidades' => 'nullable|string',
            'forma_pagamento' => 'nullable|string',
            'assin_elaboracao' => 'nullable|string|max:200',
            'assin_supervisor' => 'nullable|string|max:200',
            'assin_ordenador_despesas' => 'nullable|string|max:200',
        ]);

        $tr = TermoReferencia::create($data);

        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência criado com sucesso.');
    }

    public function show(TermoReferencia $tr)
    {
        return view('termos_referencia.show', compact('tr'));
    }

    public function edit(TermoReferencia $tr)
    {
        return view('termos_referencia.edit', compact('tr'));
    }

    public function update(Request $request, TermoReferencia $tr)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'tipo_tr' => 'required|string|max:100',
            'objeto' => 'nullable|string',
            'justificativa' => 'nullable|string',
            'escopo' => 'nullable|string',
            'requisitos' => 'nullable|string',
            'criterios_julgamento' => 'nullable|string',
            'forma_criterios_selecao_fornecedor' => 'nullable|string',
            'criterios_medicao_pagamento' => 'nullable|string',
            'fundamentacao_legal_texto' => 'nullable|string',
            'habilitacao_tecnica_exigida' => 'nullable|boolean',
            'habilitacao_tecnica_qual' => 'nullable|string',
            'habilitacao_tecnica_justificativa' => 'nullable|string',
            'habilitacao_tecnica_percentual_minimo' => 'nullable|numeric|min:0|max:100',
            'habilitacao_tecnica_documentos' => 'nullable|string',
            'criterio_sustentabilidade' => 'nullable|boolean',
            'criterio_sustentabilidade_especificar' => 'nullable|string',
            'garantia_bem_meses' => 'nullable|integer|min:0',
            'assistencia_tecnica_meses' => 'nullable|integer|min:0',
            'pagamento_prazo_dias' => 'nullable|integer|min:0',
            'garantia_contrato_tipo' => 'nullable|string',
            'garantia_contrato_percentual' => 'nullable|numeric|min:0|max:100',
            'garantia_contrato_justificativa' => 'nullable|string',
            'orcamento_sigiloso' => 'nullable|boolean',
            'forma_contratacao' => 'nullable|string|max:100',
            'criterio_julgamento_tipo' => 'nullable|string|max:100',
            'subcontratacao_permitida' => 'nullable|boolean',
            'subcontratacao_excecao' => 'nullable|string',
            'penalidades' => 'nullable|string',
            'forma_pagamento' => 'nullable|string',
            'assin_elaboracao' => 'nullable|string|max:200',
            'assin_supervisor' => 'nullable|string|max:200',
            'assin_ordenador_despesas' => 'nullable|string|max:200',
        ]);

        $tr->update($data);

        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência atualizado com sucesso.');
    }

    public function destroy(TermoReferencia $tr)
    {
        $tr->delete();

        return redirect()->route('contratacoes.termos-referencia.index')
            ->with('success', 'Termo de Referência removido.');
    }

    public function getJson()
    {
        $rows = TermoReferencia::select('id', 'titulo', 'status', 'valor_estimado')
            ->orderBy('titulo')
            ->get()
            ->map(function ($tr) {
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

        return response()->json(['data' => $rows]);
    }

    public function enviarAprovacao(TermoReferencia $tr)
    {
        $tr->update(['status' => 'em_analise']);

        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Enviado para aprovação.');
    }

    public function aprovar(TermoReferencia $tr)
    {
        $tr->update(['status' => 'finalizado']);

        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('success', 'Termo de Referência aprovado.');
    }

    public function retornarElaboracao(TermoReferencia $tr)
    {
        $tr->update(['status' => 'rascunho']);

        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('warning', 'Retornado para elaboração.');
    }

    public function reprovar(Request $request, TermoReferencia $tr)
    {
        $tr->update(['status' => 'rascunho']);

        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('warning', 'Reprovado e retornado para elaboração.');
    }

    public function pdf(TermoReferencia $tr)
    {
        $data = $this->assemblePdfData($tr);
        $pdf = Pdf::loadView('pdf.termo_referencia', $data)->setPaper('a4', 'portrait');
        $filename = 'termo_referencia_' . $tr->id . '.pdf';

        return $pdf->stream($filename);
    }

    public function docx(TermoReferencia $tr)
    {
        return redirect()->route('contratacoes.termos-referencia.show', $tr)
            ->with('info', 'Geração de DOCX em implementação.');
    }

    public function preview(Request $request)
    {
        // Dados de exemplo para visualização do layout TR (SEDUC/PA)
        $data = [
            'sec_do_objeto' => 'Contratação de empresa especializada para fornecimento de equipamentos de tecnologia educacional para as unidades escolares da rede estadual.',
            'sec_o_que_sera_contratado' => 'Aquisição de notebooks, projetores multimídia, roteadores e acessórios correlatos.',
            'sec_qual_o_motivo_da_contratacao' => 'Modernizar e ampliar o parque tecnológico escolar, atendendo diretrizes pedagógicas e demandas administrativas.',
            'sec_resultados_esperados' => 'Melhoria na infraestrutura tecnológica das escolas e suporte às práticas pedagógicas digitais.',
            'sec_fundamentacao_legal' => 'Lei Federal nº 14.133/2021 e demais dispositivos aplicáveis; regulamentos internos da SEDUC/PA.',
            'sec_natureza_e_garantia_do_servico' => 'Fornecimento de bens com garantia mínima de 12 meses e assistência técnica autorizada.',
            'sec_criterios_de_selecao' => 'Menor preço por item, observando conformidade técnica e prazos de entrega.',
            'sec_requisitos_da_contratada' => 'Capacidade técnica, documentação fiscal regular, atestados de fornecimento e assistência autorizada.',
            'sec_das_obrigacoes_das_partes' => 'A contratada deverá entregar os bens dentro do prazo; a administração realizará a conferência e aceite.',
            'sec_prazo_forma_de_pagamento_e_garantia_do_contrato' => 'Pagamento após entrega e aceite, conforme cronograma; garantia dos fabricantes por 12 meses.',
            'sec_gestao_e_fiscalizacao_do_contrato' => 'Gestão pela DRT/SEDUC; fiscalização pela CINF/SEDUC com relatórios periódicos.',
            'sec_penalidades' => 'Penalidades conforme a Lei nº 14.133/2021 e cláusulas contratuais.',
            'sec_previsao_orcamentaria' => 'Dotação orçamentária própria, conforme Plano Interno da SEDUC/PA.',
            'sec_anexo_i' => 'Planilhas e especificações detalhadas dos itens com seus códigos SIMAS.',
            'sec_especificacoes_tecnicas' => 'Especificações técnicas conforme Anexo I e normas do fabricante.',
            'itens' => [
                [
                    'lote' => '1',
                    'item' => '1',
                    'descricao' => 'Notebook 14" com processador i5, 8GB RAM, 256GB SSD',
                    'unidade' => 'UN',
                    'quantidade' => 50,
                    'valor_unitario' => 3500.00,
                    'valor_total' => 175000.00,
                    'codigo_simas' => 'SIMAS-NTB-001',
                ],
                [
                    'lote' => '1',
                    'item' => '2',
                    'descricao' => 'Projetor multimídia 3500 lumens, HDMI/VGA',
                    'unidade' => 'UN',
                    'quantidade' => 20,
                    'valor_unitario' => 2200.00,
                    'valor_total' => 44000.00,
                    'codigo_simas' => 'SIMAS-PRJ-002',
                ],
                [
                    'lote' => '2',
                    'item' => '1',
                    'descricao' => 'Roteador Wi-Fi AC1200 com VLAN, gestão remota',
                    'unidade' => 'UN',
                    'quantidade' => 40,
                    'valor_unitario' => 480.00,
                    'valor_total' => 19200.00,
                    'codigo_simas' => 'SIMAS-RTD-003',
                ],
            ],
            'assin_elaboracao_nome' => 'João Silva',
            'assin_elaboracao_cargo' => 'Analista de Infraestrutura',
            'assin_supervisor_nome' => 'Maria Oliveira',
            'assin_supervisor_cargo' => 'Coordenadora de Infraestrutura Tecnológica',
            'assin_ordenador_nome' => 'Carlos Souza',
            'assin_ordenador_cargo' => 'Ordenador de Despesas',
        ];

        return view('pdf.termo_referencia', $data);
    }

    private function assemblePdfData(TermoReferencia $tr): array
    {
        return [
            'sec_do_objeto' => (string) ($tr->objeto ?? ''),
            'sec_o_que_sera_contratado' => (string) ($tr->escopo ?? ''),
            'sec_qual_o_motivo_da_contratacao' => (string) ($tr->justificativa ?? ''),
            'sec_resultados_esperados' => (string) ($tr->criterios_julgamento ?? ''),
            'sec_fundamentacao_legal' => (string) ($tr->fundamentacao_legal_texto ?? ''),
            'sec_natureza_e_garantia_do_servico' => (string) ($tr->garantia_manutencao_assistencia ?? ''),
            'sec_criterios_de_selecao' => (string) ($tr->forma_criterios_selecao_fornecedor ?? ''),
            'sec_requisitos_da_contratada' => (string) ($tr->requisitos ?? ''),
            'sec_das_obrigacoes_das_partes' => (string) ($tr->escopo ?? ''),
            'sec_prazo_forma_de_pagamento_e_garantia_do_contrato' => (string) ($tr->forma_pagamento ?? ''),
            'sec_gestao_e_fiscalizacao_do_contrato' => (string) ($tr->modelo_gestao ?? ''),
            'sec_penalidades' => (string) ($tr->penalidades ?? ''),
            'sec_previsao_orcamentaria' => (string) ($tr->adequacao_orcamentaria ?? ''),
            'sec_anexo_i' => '',
            'sec_especificacoes_tecnicas' => (string) ($tr->especificacao_produto ?? ''),
            'itens' => $tr->itens->map(function ($item, $idx) {
                return [
                    'lote' => '',
                    'item' => (string) ($idx + 1),
                    'descricao' => (string) ($item->descricao ?? ''),
                    'unidade' => (string) ($item->unidade ?? ''),
                    'quantidade' => (float) ($item->quantidade ?? 0),
                    'valor_unitario' => (float) ($item->valor_unitario ?? 0),
                    'valor_total' => (float) ($item->valor_total ?? 0),
                    'codigo_simas' => '',
                ];
            })->toArray(),
            'assin_elaboracao_nome' => (string) ($tr->assin_elaboracao ?? $tr->responsavel_nome ?? ''),
            'assin_elaboracao_cargo' => (string) ($tr->responsavel_cargo ?? ''),
            'assin_supervisor_nome' => (string) ($tr->assin_supervisor ?? ''),
            'assin_supervisor_cargo' => '',
            'assin_ordenador_nome' => (string) ($tr->assin_ordenador_despesas ?? ''),
            'assin_ordenador_cargo' => '',
        ];
    }
}
