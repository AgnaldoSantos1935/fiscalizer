<?php

namespace App\Services;

use App\Models\AtaAdesao;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use PDF;

class AtaPdfService
{
    public function gerarAutorizacaoAdesao(AtaAdesao $adesao): string
    {
        $useJasper = (bool) (config('services.jasper.enabled') ?? false);
        if ($useJasper) {
            $jasper = App::make(JasperService::class);
            $publicUrl = rtrim(config('app.url'), '\/');
            $targetPath = 'atas/adesoes/autorizacao_'.$adesao->id.'.pdf';
            $params = [
                'adesaoId' => $adesao->id,
                'gestorNome' => (auth()->user()?->nome_completo ?? auth()->user()?->name ?? ''),
                'qrUrl' => $publicUrl.'/storage/'.$targetPath,
            ];
            $pdfBinary = $jasper->renderToPdf('atas/autorizacao.jrxml', $params);
            Storage::disk('public')->put($targetPath, $pdfBinary);
            $adesao->documento_pdf_path = $targetPath;
            $adesao->save();

            return $targetPath;
        }

        $pdf = PDF::loadView('pdf.ata_autorizacao_adesao', ['adesao' => $adesao])->setPaper('a4');
        $path = 'atas/adesoes/autorizacao_'.$adesao->id.'.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        $adesao->documento_pdf_path = $path;
        $adesao->save();

        return $path;
    }
}
