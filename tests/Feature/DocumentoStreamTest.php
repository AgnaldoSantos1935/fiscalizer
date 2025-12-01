<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\Documento;
use App\Models\DocumentoTipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentoStreamTest extends TestCase
{
    use RefreshDatabase;

    public function test_streams_contrato_pdf(): void
    {

        $contrato = Contrato::create([
            'numero' => 'C-' . time(),
            'objeto' => 'Contrato para teste de streaming',
        ]);

        $path = 'contratos/originais/test_' . time() . '.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4\n');

        $tipo = DocumentoTipo::where('slug', 'contrato_pdf')->first();

        $doc = Documento::create([
            'contrato_id' => $contrato->id,
            'tipo' => 'OUTROS',
            'documento_tipo_id' => $tipo?->id,
            'titulo' => 'Contrato PDF',
            'caminho_arquivo' => $path,
            'data_upload' => now(),
        ]);

        $this->assertTrue(Storage::disk('public')->exists($path));
        $controller = app(\App\Http\Controllers\DocumentoController::class);
        $response = $controller->stream($doc);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
