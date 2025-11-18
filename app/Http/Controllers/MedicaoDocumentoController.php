<?php

namespace App\Http\Controllers;

use App\Models\Medicao;
use App\Models\MedicaoDocumento;
use App\Models\MedicaoNotaFiscal;
use App\Services\ValidacaoMedicaoService;
use App\Services\ValidadorNotaFiscalService;
use Illuminate\Http\Request;

class MedicaoDocumentoController extends Controller
{
    public function __construct(
        protected ValidadorNotaFiscalService $validadorNF,
        protected ValidacaoMedicaoService $validacaoMedicao,
    ) {}

    /**
     * Upload de documentos da medição
     */
    public function upload(Request $request, $medicaoId)
    {
        $request->validate([
            'documentos.*' => 'required|file|max:20480',
        ]);

        $medicao = Medicao::with('documentos')->findOrFail($medicaoId);

        foreach ($request->file('documentos') as $file) {

            // Armazena
            $path = $file->store("medicoes/{$medicaoId}", 'public');

            // Descobre tipo aproximado (você pode usar o identificador avançado que montamos)
            $ext = strtolower($file->getClientOriginalExtension());
            $tipo = $this->inferirTipoDocumento($file, $ext);

            $doc = MedicaoDocumento::create([
                'medicao_id' => $medicao->id,
                'tipo' => $tipo,
                'arquivo' => $path,
                'status' => 'pendente',
            ]);

            // Se for NF, vincula/atualiza registro de NF
            if (in_array($tipo, ['nfe_xml', 'nfe_pdf', 'nfse_pdf'])) {
                $this->criarOuAtualizarNotaFiscalAPartirDoDocumento($medicao, $doc);
            }

            // Aqui você pode chamar um leitor/OCR/planilha para extrair valores
            // e preencher campos extras, se já estiver implementado.
        }

        return back()->with('success', 'Documentos enviados com sucesso. Validação automática em andamento.');
    }

    /**
     * Valida a NF ligada à medição (botão "Revalidar NF")
     */
    public function validarNF($medicaoId)
    {
        $medicao = Medicao::with(['notaFiscal', 'contrato.empresa'])->findOrFail($medicaoId);
        $nf = $medicao->notaFiscal;

        if (! $nf) {
            return back()->with('error', 'Nenhuma Nota Fiscal cadastrada para esta medição.');
        }

        $this->validadorNF->validar($nf);

        return back()->with('success', 'Nota Fiscal revalidada com sucesso.');
    }

    /**
     * Revalidar documento específico (botão "Revalidar" na tabela)
     */
    public function revalidar($medicaoId, $docId)
    {
        $medicao = Medicao::findOrFail($medicaoId);
        $doc = MedicaoDocumento::where('medicao_id', $medicaoId)->findOrFail($docId);

        // Se for NF, revalida NF
        if (in_array($doc->tipo, ['nfe_xml', 'nfe_pdf', 'nfse_pdf'])) {
            $nf = $medicao->notaFiscal;
            if ($nf) {
                $this->validadorNF->validar($nf);
            }
        }

        // Se for planilha, aqui você reprocessa a planilha e atualiza valor_extraido, etc.
        // (Depende do leitor de planilha que você montar)

        return back()->with('success', 'Documento revalidado com sucesso.');
    }

    /**
     * Substituição da NF (modal "Substituir Nota Fiscal")
     */
    public function substituirNF(Request $request, $medicaoId)
    {
        $request->validate([
            'nova_nf' => 'required|file|max:20480',
        ]);

        $medicao = Medicao::with('notaFiscal', 'documentos')->findOrFail($medicaoId);

        $file = $request->file('nova_nf');
        $path = $file->store("medicoes/{$medicaoId}/nf", 'public');

        $ext = strtolower($file->getClientOriginalExtension());
        $tipo = $this->inferirTipoDocumento($file, $ext);

        // Remove docs antigos de NF (se quiser)
        $medicao->documentos()
            ->whereIn('tipo', ['nfe_xml', 'nfe_pdf', 'nfse_pdf'])
            ->delete();

        // Cria novo doc
        $doc = MedicaoDocumento::create([
            'medicao_id' => $medicao->id,
            'tipo' => $tipo,
            'arquivo' => $path,
            'status' => 'pendente',
        ]);

        // Atualiza/Cria NF e valida
        $nf = $this->criarOuAtualizarNotaFiscalAPartirDoDocumento($medicao, $doc);
        $this->validadorNF->validar($nf);

        return back()->with('success', 'Nova Nota Fiscal enviada e validada.');
    }

    /**
     * Tela de comparação (usa a view comparacao.blade.php)
     */
    public function comparacao($medicaoId)
    {
        $medicao = Medicao::with([
            'contrato.empresa',
            'notaFiscal',
            'documentos',
            'itens',
        ])->findOrFail($medicaoId);

        $contrato = $medicao->contrato;
        $nf = $medicao->notaFiscal;

        // Valor da planilha extraído previamente e salvo em campo valor_extraido (exemplo)
        $valorPlanilha = optional(
            $medicao->documentos->where('tipo', 'planilha_medicao')->first()
        )->valor_extraido ?? 0;

        $inconsistencias = $this->validacaoMedicao->detectarInconsistencias(
            $medicao,
            $valorPlanilha,
            $nf
        );

        $resultadoValidacao = [
            'status' => count($inconsistencias) === 0 ? 'aprovado' : 'reprovado',
            'mensagem' => count($inconsistencias) === 0
                ? 'Todos os valores e documentos conferem.'
                : 'Existem inconsistências que precisam ser corrigidas antes do atesto.',
        ];

        return view('medicoes.workflow.comparacao', compact(
            'medicao',
            'contrato',
            'valorPlanilha',
            'nf',
            'inconsistencias',
            'resultadoValidacao'
        ));
    }

    /**
     * Inferir tipo do documento pelo tipo/extensão (simplificado)
     */
    protected function inferirTipoDocumento($file, string $ext): string
    {
        if ($ext === 'xml') {
            return 'nfe_xml';
        }

        if (in_array($ext, ['xls', 'xlsx'])) {
            return 'planilha_medicao';
        }

        if ($ext === 'pdf') {
            // Se quiser, você faz uma leitura do texto para diferenciar NF, NFSe, certidão etc.
            // Aqui vamos deixar genérico:
            return 'pdf';
        }

        return 'outro';
    }

    /**
     * Cria ou atualiza MedicaoNotaFiscal a partir de um MedicaoDocumento de NF
     */
    protected function criarOuAtualizarNotaFiscalAPartirDoDocumento(Medicao $medicao, MedicaoDocumento $doc): MedicaoNotaFiscal
    {
        // Aqui você faz a extração real (XML ou PDF).
        // Vou colocar um stub simples que você substitui pela leitura de fato.

        $dadosExtraidos = [
            'chave' => null,
            'numero' => null,
            'cnpj_prestador' => $medicao->contrato->empresa->cnpj,
            'cnpj_tomador' => null,
            'valor' => $medicao->valor_total,
            'tipo' => $doc->tipo,
        ];

        $nf = MedicaoNotaFiscal::updateOrCreate(
            ['medicao_id' => $medicao->id],
            [
                'chave' => $dadosExtraidos['chave'],
                'numero' => $dadosExtraidos['numero'],
                'cnpj_prestador' => $dadosExtraidos['cnpj_prestador'],
                'cnpj_tomador' => $dadosExtraidos['cnpj_tomador'],
                'valor' => $dadosExtraidos['valor'],
                'tipo' => $dadosExtraidos['tipo'],
                'status' => 'pendente',
            ]
        );

        return $nf;
    }
}
