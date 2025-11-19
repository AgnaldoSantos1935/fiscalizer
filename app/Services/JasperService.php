<?php

namespace App\Services;

use PHPJasper\PHPJasper;
use PHPJasper\Options; 
use Illuminate\Support\Facades\Storage;

class JasperService
{
    /**
     * Gera relatório usando JasperReports (via JasperStarter).
     * @param string $jrxmlPath Caminho para template JRXML
     * @param array $parameters Parâmetros do relatório
     * @param string $outputBase Caminho base de saída (sem extensão)
     * @param array $formats Formatos [pdf, html, xls]
     */
    public function process(string $jrxmlPath, array $parameters, string $outputBase, array $formats = ['pdf']): string
    {
        // garante diretório
        $dir = dirname($outputBase);
        if (! is_dir($dir)) {
            Storage::makeDirectory(str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $dir));
        }

        $options = new Options();
        $options->setFormat($formats);
        $options->setLocale('pt_BR');

        // conexão com banco (MySQL)
        $options->setDbConnection([
            'driver' => 'mysql',
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'host' => env('DB_HOST'),
            'database' => env('DB_DATABASE'),
            'port' => env('DB_PORT'),
        ]);

        // parâmetros
        if (! empty($parameters)) {
            $options->setParameters($parameters);
        }

        $jasper = new PHPJasper();
        $jasper->process(
            $jrxmlPath,
            $outputBase,
            $options
        )->execute();

        return $outputBase . '.pdf';
    }
}
