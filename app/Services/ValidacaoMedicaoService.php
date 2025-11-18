<?php

namespace App\Services;

use App\Models\Medicao;

class ValidacaoMedicaoService
{
    /**
     * Detecta inconsistências entre contrato, medição, planilha e NF.
     */
    public function detectarInconsistencias(Medicao $medicao, float $valorPlanilha, $nf): array
    {
        $inconsistencias = [];

        $contrato = $medicao->contrato;
        $empresa = $contrato->empresa;

        // 1) Valores
        if ($medicao->valor_total > $contrato->valor_global) {
            $inconsistencias[] =
                'O valor da medição (R$ '.number_format($medicao->valor_total, 2, ',', '.').
                ') é maior que o valor global do contrato (R$ '.
                number_format($contrato->valor_global, 2, ',', '.').').';
        }

        if ($valorPlanilha != $medicao->valor_total) {
            $inconsistencias[] =
                'O valor da planilha (R$ '.number_format($valorPlanilha, 2, ',', '.').
                ') difere do valor informado da medição (R$ '.
                number_format($medicao->valor_total, 2, ',', '.').').';
        }

        if ($nf && $nf->valor != $medicao->valor_total) {
            $inconsistencias[] =
                'O valor da Nota Fiscal (R$ '.number_format($nf->valor, 2, ',', '.').
                ') é diferente do valor da medição (R$ '.
                number_format($medicao->valor_total, 2, ',', '.').').';
        }

        // 2) CNPJ
        if ($nf && $nf->cnpj_prestador != $empresa->cnpj) {
            $inconsistencias[] =
                'O CNPJ do emitente da Nota Fiscal ('.$nf->cnpj_prestador.
                ') não corresponde ao CNPJ da empresa contratada ('.$empresa->cnpj.').';
        }

        // 3) Datas e vigência
        if ($nf && isset($nf->data_emissao)) {
            if ($nf->data_emissao < $contrato->data_inicio ||
                $nf->data_emissao > $contrato->data_fim) {

                $inconsistencias[] =
                    'A data de emissão da Nota Fiscal ('.$nf->data_emissao->format('d/m/Y').
                    ') está fora da vigência do contrato ('.
                    $contrato->data_inicio->format('d/m/Y').' a '.
                    $contrato->data_fim->format('d/m/Y').').';
            }

            if ($medicao->periodo_inicio && $medicao->periodo_fim &&
                ($nf->data_emissao < $medicao->periodo_inicio ||
                 $nf->data_emissao > $medicao->periodo_fim)) {

                $inconsistencias[] =
                    'A Nota Fiscal foi emitida em '.$nf->data_emissao->format('d/m/Y').
                    ', fora do período da medição ('.
                    $medicao->periodo_inicio->format('d/m/Y').' a '.
                    $medicao->periodo_fim->format('d/m/Y').').';
            }
        }

        // 4) Itens executados
        if ($medicao->relationLoaded('itens') || method_exists($medicao, 'itens')) {
            foreach ($medicao->itens as $item) {
                if ($item->quantidade_executada < 0) {
                    $inconsistencias[] =
                        "O item '".$item->descricao."' possui quantidade negativa.";
                }

                if ($item->quantidade_planejada !== null &&
                    $item->quantidade_executada > $item->quantidade_planejada) {

                    $inconsistencias[] =
                        "O item '".$item->descricao."' excede a quantidade planejada. ".
                        'Executado: '.$item->quantidade_executada.
                        ' / Planejado: '.$item->quantidade_planejada.'.';
                }
            }
        }

        // 5) Documentos obrigatórios
        $documentosObrigatorios = [
            'planilha_medicao',
            'relatorio_execucao',
        ];

        foreach ($documentosObrigatorios as $tipoDoc) {
            if (! $medicao->documentos->where('tipo', $tipoDoc)->count()) {
                $inconsistencias[] =
                    "O documento obrigatório '".str_replace('_', ' ', $tipoDoc)."' não foi enviado.";
            }
        }

        // 6) Certidões
        $certidoes = $medicao->documentos->where('tipo', 'certidao');

        foreach ($certidoes as $certidao) {
            if ($certidao->data_validade && $certidao->data_validade < today()) {
                $inconsistencias[] =
                    "A certidão '".$certidao->nome."' está vencida desde ".
                    $certidao->data_validade->format('d/m/Y').'.';
            }
        }

        // 7) NF inválida
        if ($nf && $nf->status == 'invalido') {
            $inconsistencias[] = 'A Nota Fiscal é inválida: '.$nf->mensagem.'.';
        }

        if ($nf && $nf->status == 'erro') {
            $inconsistencias[] = 'Ocorreu um erro ao validar a Nota Fiscal: '.$nf->mensagem.'.';
        }

        // 8) Valores zero
        if ($medicao->valor_total == 0) {
            $inconsistencias[] = 'Medição não pode possuir valor total igual a ZERO.';
        }

        if ($valorPlanilha == 0) {
            $inconsistencias[] = 'Planilha de medição contém valor total ZERO.';
        }

        if ($nf && $nf->valor == 0) {
            $inconsistencias[] = 'Nota Fiscal emitida com valor ZERO.';
        }

        // 9) Diferença percentual planilha x medição
        if ($valorPlanilha > 0) {
            $percentual = abs(($valorPlanilha - $medicao->valor_total) / $valorPlanilha) * 100;

            if ($percentual > 5) {
                $inconsistencias[] =
                    'Diferença percentual entre planilha e medição é de '.
                    number_format($percentual, 2).' % (tolerância máxima: 5%).';
            }
        }

        // ... (regras que já fizemos: contrato, NF, documentos, etc.)

        // 🔹 Regras específicas de PF / UST / horas / equipe:
        $apfService = app(\App\Services\ValidacaoApfUstService::class);
        $incsApf = $apfService->detectarInconsistenciasApf($medicao);

        $inconsistencias = array_merge($inconsistencias, $incsApf);

        return $inconsistencias;
    }
}
