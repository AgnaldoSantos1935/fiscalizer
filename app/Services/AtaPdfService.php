<?php

namespace App\Services;

use App\Models\AtaAdesao;
use Illuminate\Support\Facades\Storage;
use PDF;

class AtaPdfService
{
    public function gerarAutorizacaoAdesao(AtaAdesao $adesao): string
    {
        $pdf = PDF::loadView('pdf.ata_autorizacao_adesao', ['adesao' => $adesao])->setPaper('a4');
        $path = 'atas/adesoes/autorizacao_' . $adesao->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        $adesao->documento_pdf_path = $path;
        $adesao->save();

        return $path;
    }
}
