<?php

namespace App\Jobs;

use App\Models\DocumentoTecnico;
use App\Services\DocumentoTecnicoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessarDocumentoTecnicoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public DocumentoTecnico $doc) {}

    public function handle(DocumentoTecnicoService $service)
    {
        $service->processarDocumento($this->doc);
    }
}
