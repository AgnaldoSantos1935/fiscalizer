<?php

// app/Services/WhatsAppService.php

namespace App\Services;

use App\Models\Medicao;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use PDF;

class PdfService
{
    public function gerarBoletim(Medicao $medicao): string
    {
        $pdf = Pdf::loadView('pdfs.boletim-medicao', compact('medicao'));
        $file = "boletins/boletim_{$medicao->id}.pdf";
        Storage::disk('public')->put($file, $pdf->output());

        return $file;
    }

    public function gerarAtesto(Medicao $medicao, User $fiscal): string
    {
        $hash = hash('sha256', $medicao->id.$fiscal->id.now());

        $pdf = PDF::loadView('pdfs.atesto-servicos', [
            'medicao' => $medicao,
            'fiscal' => $fiscal,
            'hash' => $hash,
        ]);

        $file = "atestados/atesto_{$medicao->id}.pdf";
        Storage::disk('public')->put($file, $pdf->output());

        // salva no banco
        MedicaoAtesto::create([
            'medicao_id' => $medicao->id,
            'fiscal_id' => $fiscal->id,
            'hash_assinatura' => $hash,
            'data_assinatura' => now(),
            'arquivo_pdf' => $file,
        ]);

        return $file;
    }
}
