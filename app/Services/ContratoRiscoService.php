<?php

namespace App\Services;

use App\Models\Contrato;

class ContratoRiscoService
{
    public function calcular(Contrato $contrato, array $inconsistenciasIa = []): array
    {
        $score = 100;
        $detalhes = [];

        // 1) Inconsistências vindas da IA (texto)
        foreach ($inconsistenciasIa as $inc) {
            $peso = $this->pesoPorInconsistencia($inc);
            $score -= $peso;
            $detalhes[] = [
                'fonte' => 'IA',
                'descricao' => $inc,
                'peso' => $peso,
            ];
        }

        // 2) Regras determinísticas (datas, valores, cláusulas)
        $regras = $this->validacoesDeterministicas($contrato);

        foreach ($regras as $regra) {
            if ($regra['falha']) {
                $score -= $regra['peso'];
                $detalhes[] = [
                    'fonte' => 'Regra',
                    'descricao' => $regra['descricao'],
                    'peso' => $regra['peso'],
                ];
            }
        }

        // clamp entre 0 e 100
        $score = max(0, min(100, $score));

        $nivel = match (true) {
            $score < 40 => 'Crítico',
            $score < 60 => 'Alto',
            $score < 80 => 'Médio',
            default => 'Baixo',
        };

        return [
            'score' => $score,
            'nivel' => $nivel,
            'detalhes' => $detalhes,
        ];
    }

    /**
     * Aplica pesos com base em palavras-chave das inconsistências da IA
     */
    protected function pesoPorInconsistencia(string $inc): int
    {
        $txt = mb_strtolower($inc, 'UTF-8');

        return match (true) {
            str_contains($txt, 'sem cláusula de sanções'),
            str_contains($txt, 'sem sanções') => 25,

            str_contains($txt, 'sem termo de referência'),
            str_contains($txt, 'sem tr '),
            str_contains($txt, 'sem projeto básico'),
            str_contains($txt, 'sem etp') => 20,

            str_contains($txt, 'sem critério de aceite'),
            str_contains($txt, 'sem critérios de aceite') => 20,

            str_contains($txt, 'vigência invertida'),
            str_contains($txt, 'vigência acima do permitido') => 20,

            str_contains($txt, 'objeto genérico'),
            str_contains($txt, 'objeto vago') => 15,

            str_contains($txt, 'valor global não confere'),
            str_contains($txt, 'valores incoerentes') => 15,

            str_contains($txt, 'sem sla'),
            str_contains($txt, 'sem acordo de nível de serviço') => 20,

            str_contains($txt, 'risco alto'),
            str_contains($txt, 'risco elevado') => 15,

            default => 5,
        };
    }

    /**
     * Validações "duronas" baseadas nos campos do contrato
     */
    protected function validacoesDeterministicas(Contrato $c): array
    {
        $regras = [];

        // Datas
        $regras[] = [
            'descricao' => 'Data de assinatura posterior ao início da vigência.',
            'peso' => 20,
            'falha' => $c->data_assinatura && $c->data_inicio_vigencia &&
                           $c->data_assinatura->gt($c->data_inicio_vigencia),
        ];

        $regras[] = [
            'descricao' => 'Data de fim de vigência anterior ao início.',
            'peso' => 20,
            'falha' => $c->data_inicio_vigencia && $c->data_fim_vigencia &&
                           $c->data_fim_vigencia->lt($c->data_inicio_vigencia),
        ];

        // Valores
        $regras[] = [
            'descricao' => 'Valor global diferente de valor mensal x quantidade de meses.',
            'peso' => 15,
            'falha' => $c->valor_global && $c->valor_mensal && $c->quantidade_meses &&
                           round($c->valor_global, 2) !== round($c->valor_mensal * $c->quantidade_meses, 2),
        ];

        // Cláusulas obrigatórias no JSON
        $clausulas = $c->clausulas ? (array) json_decode($c->clausulas, true) : [];

        $obrigatorias = [
            'forma_pagamento' => 'Ausência de cláusula de forma de pagamento.',
            'rescisao' => 'Ausência de cláusula de rescisão.',
            'sanções' => 'Ausência de cláusula de sanções.',
        ];

        foreach ($obrigatorias as $campo => $msg) {
            $regras[] = [
                'descricao' => $msg,
                'peso' => 20,
                'falha' => empty($clausulas[$campo]),
            ];
        }

        // TI sem SLA
        $isTi = str_contains(mb_strtolower($c->objeto, 'UTF-8'), 'software')
             || str_contains(mb_strtolower($c->objeto, 'UTF-8'), 'sistema');

        $regras[] = [
            'descricao' => 'Contrato de TI sem cláusula de SLA definida.',
            'peso' => 20,
            'falha' => $isTi && empty($clausulas['indicadores']),
        ];

        return $regras;
    }
}
