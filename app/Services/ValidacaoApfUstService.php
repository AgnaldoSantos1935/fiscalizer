<?php

namespace App\Services;

use App\Models\Medicao;
use App\Models\MedicaoItem;

class ValidacaoApfUstService
{
    public function detectarDuplicidadeComIa(Medicao $medicao): array
    {
        $inconsistencias = [];
        $ai = app(\App\Services\SimilarityAiService::class);

        // pega itens desta medição
        $itensAtuais = $medicao->itens;

        if ($itensAtuais->isEmpty()) {
            return $inconsistencias;
        }

        // pega itens de outras medições DO MESMO CONTRATO nos últimos X meses
        $itensAnteriores = \App\Models\MedicaoItem::whereHas('medicao', function ($q) use ($medicao) {
            $q->where('contrato_id', $medicao->contrato_id)
                ->where('id', '<>', $medicao->id)
                ->where('created_at', '>=', now()->subMonths(6));
        })
            ->get();

        foreach ($itensAtuais as $item) {
            foreach ($itensAnteriores as $old) {

                // regra rápida: mesmo módulo e PF muito parecido
                if (
                    $item->modulo_id && $old->modulo_id &&
                    $item->modulo_id == $old->modulo_id &&
                    abs($item->quantidade_pf - $old->quantidade_pf) <= 2
                ) {

                    // IA entra aqui para avaliar a similaridade das descrições
                    $score = $ai->similarity($item->descricao ?? '', $old->descricao ?? '');

                    if ($score >= 0.85) {
                        $inconsistencias[] =
                            'Possível duplicidade detectada entre medições. '.
                            "Item atual '{$item->descricao}' (Medição #{$medicao->id}) ".
                            "é muito similar ao item '{$old->descricao}' (Medição #{$old->medicao_id}), ".
                            'similaridade IA = '.round($score * 100, 2).'%.';
                    }
                }
            }
        }

        return $inconsistencias;
    }

    public function detectarInconsistenciasApf(Medicao $medicao): array
    {
        $inconsistencias = [];

        $contrato = $medicao->contrato;
        $param = $contrato->parametros_apf ?? null; // pode ser relação ou json

        // valores de referência (ajuste pro seu modelo real)
        $precoPf = $param->preco_pf ?? 0;
        $precoUst = $param->preco_ust ?? 0;
        $horasPorPfRef = $param->horas_por_pf ?? 8;   // ex: 8h/PF
        $minHorasPorPf = $param->min_horas_por_pf ?? 2;   // faixa aceitável
        $maxHorasPorPf = $param->max_horas_por_pf ?? 40;
        $horasMesPessoa = $param->horas_mes_pessoa ?? 160; // jornada
        $pfPorPessoaMesRef = $param->pf_pessoa_mes_ref ?? 60;  // produtividade ref.

        // agrega dados da medição
        $totalPf = 0;
        $totalUst = 0;
        $totalHoras = 0;
        $totalValorCalculado = 0;
        $totalPessoas = 0;

        foreach ($medicao->itens as $item) {
            if ($item->tipo_contagem === 'PF') {
                $totalPf += $item->quantidade_pf;
                $totalValorCalculado += $item->quantidade_pf * ($item->valor_unitario ?: $precoPf);
            }

            if ($item->tipo_contagem === 'UST') {
                $totalUst += $item->quantidade_pf; // ou quantidade_ust
                $totalValorCalculado += $item->quantidade_pf * ($item->valor_unitario ?: $precoUst);
            }

            $totalHoras += $item->horas_executadas ?? 0;
            $totalPessoas += $item->qtd_pessoas ?? 0;

            // --- validações por item ---
            if ($item->tipo_contagem && $item->quantidade_pf > 0 && ($item->horas_executadas ?? 0) == 0) {
                $inconsistencias[] =
                    "Item '{$item->descricao}' possui pontos ({$item->quantidade_pf}) ".
                    'mas horas executadas igual a zero.';
            }

            if ($item->valor_total !== null && $item->valor_total != round($item->quantidade_pf * ($item->valor_unitario ?: 0), 2)) {
                $inconsistencias[] =
                    "Valor total do item '{$item->descricao}' não confere com ".
                    "quantidade ({$item->quantidade_pf}) x valor unitário (R$ ".
                    number_format($item->valor_unitario, 2, ',', '.').').';
            }

            if (($item->qtd_pessoas ?? 0) === 0 && ($item->horas_executadas ?? 0) > 0) {
                $inconsistencias[] =
                    "Item '{$item->descricao}' possui horas executadas ".
                    'sem quantidade de pessoas informada.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 1) Coerência PF/UST x Valor cobrado
        |--------------------------------------------------------------------------
        */
        if ($medicao->valor_total && $totalValorCalculado > 0) {
            $dif = abs($medicao->valor_total - $totalValorCalculado);

            // tolerância de 1% por exemplo
            $limite = $totalValorCalculado * 0.01;

            if ($dif > $limite) {
                $inconsistencias[] =
                    'Valor total da medição (R$ '.number_format($medicao->valor_total, 2, ',', '.').
                    ') não confere com o valor calculado pelos pontos (R$ '.
                    number_format($totalValorCalculado, 2, ',', '.').'). Diferença de R$ '.
                    number_format($dif, 2, ',', '.').'.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2) Coerência PF x horas (horas por PF)
        |--------------------------------------------------------------------------
        */
        if ($totalPf > 0 && $totalHoras > 0) {
            $horasPorPf = $totalHoras / $totalPf;

            if ($horasPorPf < $minHorasPorPf) {
                $inconsistencias[] =
                    'Produtividade muito alta: '.number_format($horasPorPf, 2).
                    " h/PF (mínimo esperado: {$minHorasPorPf} h/PF). Possível subdeclaração de horas ".
                    'ou contagem de PF acima do real.';
            }

            if ($horasPorPf > $maxHorasPorPf) {
                $inconsistencias[] =
                    'Produtividade muito baixa: '.number_format($horasPorPf, 2).
                    " h/PF (máximo recomendado: {$maxHorasPorPf} h/PF). Possível superdimensionamento ".
                    'de horas ou subcontagem de pontos.';
            }

            // compara com referência contratual
            if ($horasPorPf > $horasPorPfRef * 1.5) {
                $inconsistencias[] =
                    'Horas por PF ('.number_format($horasPorPf, 2).
                    " h/PF) muito acima da referência contratual ({$horasPorPfRef} h/PF).";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3) Coerência horas x equipe (capacidade humana)
        |--------------------------------------------------------------------------
        */
        if ($totalPessoas > 0 && $totalHoras > 0 && $medicao->periodo_inicio && $medicao->periodo_fim) {

            $dias = $medicao->periodo_inicio->diffInDays($medicao->periodo_fim) + 1;
            $mesesAprox = max($dias / 30, 0.25); // evita zero

            $capacidadeMax = $totalPessoas * $horasMesPessoa * $mesesAprox;
            $fatorTol = 1.1; // 10% de tolerância

            if ($totalHoras > $capacidadeMax * $fatorTol) {
                $inconsistencias[] =
                    "Horas declaradas ({$totalHoras} h) excedem a capacidade teórica da equipe ".
                    "({$totalPessoas} pessoas x {$horasMesPessoa} h/mês x ".
                    number_format($mesesAprox, 2).' mês(es)).';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4) Produtividade PF por pessoa-mês
        |--------------------------------------------------------------------------
        */
        if ($totalPessoas > 0 && $totalPf > 0 && $medicao->periodo_inicio && $medicao->periodo_fim) {

            $dias = $medicao->periodo_inicio->diffInDays($medicao->periodo_fim) + 1;
            $mesesAprox = max($dias / 30, 0.25);

            $pfPorPessoaMes = $totalPf / ($totalPessoas * $mesesAprox);

            if ($pfPorPessoaMes < $pfPorPessoaMesRef * 0.3) {
                $inconsistencias[] =
                    'Produtividade baixa: '.number_format($pfPorPessoaMes, 2).
                    " PF/pessoa-mês (referência contratual: {$pfPorPessoaMesRef} PF/pessoa-mês).";
            }

            if ($pfPorPessoaMes > $pfPorPessoaMesRef * 2) {
                $inconsistencias[] =
                    'Produtividade muito alta: '.number_format($pfPorPessoaMes, 2).
                    " PF/pessoa-mês (acima do dobro da referência contratual de {$pfPorPessoaMesRef}). ".
                    'Possível contagem superestimada de PF.';
            }
            /*
          |--------------------------------------------------------------------------
          | 5) Duplicidade de serviços / requisitos / demandas
          |--------------------------------------------------------------------------
          */
            foreach ($medicao->itens as $item) {

                // A) duplicidade por demanda
                if (
                    MedicaoItem::where('demanda_id', $item->demanda_id)
                        ->where('medicao_id', '<>', $medicao->id)->exists()
                ) {

                    $inconsistencias[] =
                        "A demanda '{$item->demanda_id}' já foi medida em outra medição.";
                }

                // B) duplicidade por requisito
                if (
                    MedicaoItem::where('requisito_id', $item->requisito_id)
                        ->where('medicao_id', '<>', $medicao->id)->exists()
                ) {

                    $inconsistencias[] =
                        "O requisito '{$item->requisito_id}' já foi medido anteriormente.";
                }

                // C) duplicidade por hash
                if (
                    MedicaoItem::where('item_unico_hash', $item->item_unico_hash)
                        ->where('id', '<>', $item->id)->exists()
                ) {

                    $inconsistencias[] =
                        'Item duplicado: hash único já consta em outra medição.';
                }

                // D) duplicidade textual
                foreach (MedicaoItem::where('medicao_id', '<>', $medicao->id)->get() as $ant) {
                    similar_text($item->descricao, $ant->descricao, $percent);
                    if ($percent > 85) {
                        $inconsistencias[] =
                            "Alta similaridade entre: '{$item->descricao}' e '{$ant->descricao}'. Possível duplicidade.";
                    }
                }
            }

        }
        // Regras APF/UST/hora/equipe
        $apfIncs = $this->detectarInconsistenciasApf($medicao);
        $inconsistencias = array_merge($inconsistencias, $apfIncs);

        // 🔥 Regras de duplicidade com IA
        $dupIa = $this->detectarDuplicidadeComIa($medicao);
        $inconsistencias = array_merge($inconsistencias, $dupIa);

        return $inconsistencias;
    }
}
