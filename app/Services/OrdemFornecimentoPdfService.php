<?php

namespace App\Services;

use App\Models\Contrato;
use App\Models\Empenho;
use App\Models\OrdemFornecimento;
use Illuminate\Support\Facades\Storage;
use PDF;

class OrdemFornecimentoPdfService
{
    /**
     * Gera a Ordem de Fornecimento (OF) a partir do contrato e da nota de empenho.
     */
    public function gerarParaEmpenho(Contrato $contrato, Empenho $empenho): OrdemFornecimento
    {
        $ano = now()->year;
        $sequencial = (OrdemFornecimento::where('ano_of', $ano)->max('id') ?? 0) + 1;
        $numeroOf = str_pad($sequencial, 4, '0', STR_PAD_LEFT) . '/' . $ano;

        $itens = $empenho->itens()->get(['descricao', 'quantidade', 'valor_unitario', 'valor_total'])
            ->map(fn ($i) => [
                'descricao' => $i->descricao,
                'quantidade' => $i->quantidade,
                'valor_unitario' => $i->valor_unitario,
                'valor_total' => $i->valor_total,
            ])->values()->toArray();

        // Metadados default/consolidados
        $orgao = config('app.name');
        $contratada = $contrato->contratada;
        $gestor = $contrato->gestor; // Pessoa
        $fiscalAdm = $contrato->fiscalAdministrativo; // Pessoa
        $gestorCargo = optional(optional($gestor)->servidor)->cargo;
        $fiscalCargo = optional(optional($fiscalAdm)->servidor)->cargo;

        // Cria registro inicial da OF com metadados
        $of = OrdemFornecimento::create([
            'contrato_id' => $contrato->id,
            'empenho_id' => $empenho->id,
            'numero_of' => $numeroOf,
            'ano_of' => $ano,
            'data_emissao' => now(),
            'arquivo_pdf' => '',
            'itens_json' => $itens,

            'orgao_entidade' => $orgao,
            'unidade_requisitante' => null,
            'cnpj_orgao' => null,

            'contrato_numero' => $contrato->numero,
            'processo_contratacao' => $empenho->processo ?? $contrato->processo_origem,
            'modalidade' => $contrato->modalidade,
            'vigencia_inicio' => $contrato->data_inicio_vigencia,
            'vigencia_fim' => $contrato->data_fim_vigencia,
            'fundamentacao_legal' => 'Lei nº 14.133/2021',

            'contratada_razao_social' => optional($contratada)->razao_social,
            'contratada_cnpj' => optional($contratada)->cnpj,
            'contratada_endereco' => optional($contratada)->endereco,
            'contratada_representante' => null,
            'contratada_contato' => trim(implode(' ', array_filter([optional($contratada)->telefone, optional($contratada)->email]))),

            'prazo_entrega_dias' => null,
            'local_entrega' => null,
            'horario_entrega' => null,
            'recebimento_condicoes' => 'Recebimento provisório: conferência dos bens/serviços; Recebimento definitivo: após verificação da conformidade com o contrato; Recusa do item caso esteja em desacordo com as especificações contratuais.',

            'obrigacoes_contratada' => 'Cumprir integralmente as condições do contrato e desta ordem; Garantir qualidade e conformidade técnica; Substituir itens em desacordo; Observar prazos e normas aplicáveis (art. 81, Lei 14.133).',
            'obrigacoes_administracao' => 'Disponibilizar condições para entrega e conferência; Efetuar pagamento conforme contrato; Registrar e comunicar irregularidades.',
            'sancoes' => 'Conforme Contrato e arts. 156 a 168 da Lei nº 14.133/2021.',

            'autoridade_nome' => null,
            'autoridade_cargo' => null,
            'gestor_nome' => optional($gestor)->nome_completo,
            'gestor_portaria' => null,
            'fiscal_nome' => optional($fiscalAdm)->nome_completo,
            'fiscal_portaria' => null,
        ]);

        // Gera PDF via Blade
        $pdf = PDF::loadView('pdf.ordem_fornecimento', [
            'of' => $of,
            'contrato' => $contrato,
            'empenho' => $empenho,
            'itens' => $itens,
        ])->setPaper('a4');

        $filePath = "ordens_fornecimento/of_{$of->id}.pdf";
        Storage::disk('public')->put($filePath, $pdf->output());

        $of->arquivo_pdf = $filePath;
        $of->save();

        return $of;
    }
}
