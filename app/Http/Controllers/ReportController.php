<?php

namespace App\Http\Controllers;

use App\Services\JasperService;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function jasperDemo(JasperService $jasper)
    {
        try {
            $jrxml = resource_path('reports/demo_hosts.jrxml');
            $output = storage_path('app/reports/demo_hosts_' . time());

            if (! file_exists($jrxml)) {
                return response()->view('errors.jasper', [
                    'message' => 'Template JRXML não encontrado em resources/reports/demo_hosts.jrxml',
                ], 500);
            }

            $pdfPath = $jasper->process($jrxml, [], $output, ['pdf']);
            if (! file_exists($pdfPath)) {
                return response()->view('errors.jasper', [
                    'message' => 'Falha ao gerar o PDF com JasperReports. Verifique instalação do JasperStarter e drivers MySQL.',
                ], 500);
            }

            return response()->file($pdfPath, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Throwable $e) {
            return response()->view('errors.jasper', [
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}