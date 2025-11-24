<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class JasperReportsController extends Controller
{
    public function contratos(): Response
    {
        try {
            $contratos = Contrato::query()
                ->leftJoin('empresas', 'empresas.id', '=', 'contratos.contratada_id')
                ->orderByDesc('contratos.created_at')
                ->limit(200)
                ->get([
                    'contratos.id',
                    'contratos.numero as num_contrato',
                    DB::raw('COALESCE(empresas.razao_social, contratos.empresa_razao_social) as contratada'),
                    'contratos.data_inicio_vigencia as data_inicio',
                    'contratos.data_fim_vigencia as data_fim',
                    'contratos.valor_global as valor_total',
                ]);
            $pdf = Pdf::loadView('pdf.contratos_list', compact('contratos'));

            return new Response($pdf->stream('contratos.pdf'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="contratos.pdf"',
            ]);
        } catch (Throwable $e) {
            return new Response('Falha ao gerar PDF de contratos: ' . $e->getMessage(), 500);
        }
    }
}
