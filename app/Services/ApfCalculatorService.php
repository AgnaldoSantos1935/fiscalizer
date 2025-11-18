<?php

// app/Services/ApfCalculatorService.php

namespace App\Services;

class ApfCalculatorService
{
    // Tabela de pesos IFPUG simplificada: [tipo][complexidade]
    protected static array $pesos = [
        'EE' => ['baixa' => 3,  'media' => 4,  'alta' => 6],
        'SE' => ['baixa' => 4,  'media' => 5,  'alta' => 7],
        'CE' => ['baixa' => 3,  'media' => 4,  'alta' => 6],
        'ALI' => ['baixa' => 7,  'media' => 10, 'alta' => 15],
        'AIE' => ['baixa' => 5,  'media' => 7,  'alta' => 10],
    ];

    /**
     * @param  array  $funcoes  Ex: [
     *                          ['tipo' => 'EE', 'complexidade' => 'media', 'quantidade' => 2],
     *                          ['tipo' => 'ALI','complexidade' => 'alta', 'quantidade' => 1],
     *                          ]
     */
    public static function calcularPf(array $funcoes): float
    {
        $total = 0;

        foreach ($funcoes as $f) {
            $tipo = strtoupper($f['tipo']);
            $comp = strtolower($f['complexidade']);
            $qtd = (int) ($f['quantidade'] ?? 1);

            $peso = self::$pesos[$tipo][$comp] ?? 0;
            $total += $peso * $qtd;
        }

        return $total;
    }

    public static function pfParaUst(float $pf, float $fatorPfPorUst = 0.5): float
    {
        // Exemplo: cada 0.5 PF = 1 UST
        return round($pf / $fatorPfPorUst, 2);
    }
}
