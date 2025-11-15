<?php

// app/Services/ApfCalculatorService.php
namespace App\Services;

class MedicaoAtestoPdfService
{

    public function gerar(Medicao $medicao)
    {
        $medicao->load('contrato', 'itensSoftware', 'itensTelco', 'itensFixo');

        $pdf = PDF::loadView('pdf.medicao_atesto', [
            'medicao' => $medicao,
        ])->setPaper('a4');

        $path = "medicoes/atestos/medicao_{$medicao->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

}
