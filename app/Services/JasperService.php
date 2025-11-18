<?php

namespace App\Services;

use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class JasperService
{
    public function renderToPdf(string $reportRelativePath, array $params = []): string
    {
        $starter = config('services.jasper.starter_path');
        $templatesDir = rtrim(config('services.jasper.templates_dir', resource_path('reports')), '\\/');
        $reportPath = $templatesDir.DIRECTORY_SEPARATOR.$reportRelativePath;
        if (! is_file($reportPath)) {
            $alt = preg_replace('/\.jrxml$/', '.jasper', $reportPath);
            if (is_file($alt)) {
                $reportPath = $alt;
            }
        }

        if (! $starter) {
            $finder = new ExecutableFinder;
            $starter = $finder->find('jasperstarter');
        }
        if (! $starter) {
            throw new RuntimeException('JasperStarter não encontrado. Configure services.jasper.starter_path.');
        }
        if (! is_file($reportPath)) {
            throw new RuntimeException('Template Jasper não encontrado: '.$reportPath);
        }

        $tmpDir = storage_path('app/tmp/jasper');
        if (! is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }

        $cmd = [
            $starter,
            'process',
            $reportPath,
            '-f',
            'pdf',
            '-o',
            $tmpDir,
        ];

        $dbEnabled = (bool) (config('services.jasper.db_enabled') ?? false);
        if ($dbEnabled) {
            $cmd = array_merge($cmd, [
                '-t', 'mysql',
                '-u', env('DB_USERNAME'),
                '-p', env('DB_PASSWORD'),
                '-H', env('DB_HOST'),
                '-n', env('DB_DATABASE'),
                '--db-port', env('DB_PORT', '3306'),
            ]);
        }

        if (! empty($params)) {
            $cmd[] = '-P';
            $cmd = array_merge($cmd, $this->formatParams($params));
        }

        $process = new Process($cmd);
        $process->setTimeout((int) (config('services.jasper.timeout', 60)));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('Falha ao gerar PDF com Jasper: '.$process->getErrorOutput());
        }

        $baseName = pathinfo($reportPath, PATHINFO_FILENAME);
        $outFile = $tmpDir.DIRECTORY_SEPARATOR.$baseName.'.pdf';
        if (! is_file($outFile)) {
            throw new RuntimeException('PDF não gerado por Jasper: '.$outFile);
        }

        return file_get_contents($outFile);
    }

    private function formatParams(array $params): array
    {
        $pairs = [];
        foreach ($params as $k => $v) {
            $pairs[] = $k.'='.$this->escapeParam((string) $v);
        }

        return $pairs;
    }

    private function escapeParam(string $value): string
    {
        return str_replace(['"', "'"], '', $value);
    }
}
