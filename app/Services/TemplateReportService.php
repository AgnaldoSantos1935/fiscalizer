<?php

namespace App\Services;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class TemplateReportService
{
    /**
     * Gera um DOCX a partir de um modelo com placeholders.
     * Placeholders devem usar a sintaxe ${chave} dentro do .docx do Word.
     *
     * @param  string  $templatePath  Caminho absoluto ou relativo ao modelo .docx
     * @param  array  $variables  Mapa [chave => valor] para substituir
     * @param  string|null  $outputName  Nome base do arquivo de saída (sem extensão)
     * @return string Caminho completo do arquivo gerado (.docx)
     */
    public function generateDocx(string $templatePath, array $variables, ?string $outputName = null): string
    {
        $absoluteTemplate = $this->resolvePath($templatePath);
        if (! file_exists($absoluteTemplate)) {
            throw new \InvalidArgumentException("Modelo DOCX não encontrado: {$absoluteTemplate}");
        }

        $processor = new TemplateProcessor($absoluteTemplate);
        foreach ($variables as $key => $value) {
            // Substitui ${key} mantendo formatação do documento
            $processor->setValue($key, (string) $value);
        }

        $outputDir = storage_path('app/reports');
        if (! is_dir($outputDir)) {
            @mkdir($outputDir, 0775, true);
        }

        $safeName = $outputName ?: ('report_' . Str::slug(pathinfo($absoluteTemplate, PATHINFO_FILENAME)) . '_' . time());
        $outputFile = $outputDir . DIRECTORY_SEPARATOR . $safeName . '.docx';
        $processor->saveAs($outputFile);

        return $outputFile;
    }

    /**
     * Stub para geração de XLSX via template. Depende de ext-zip e PhpSpreadsheet.
     * Mantemos a assinatura para futura implementação.
     */
    public function generateXlsx(string $templatePath, array $variables, ?string $outputName = null): string
    {
        if (! extension_loaded('zip')) {
            throw new \RuntimeException('Geração de XLSX indisponível: extensão PHP \'zip\' não está habilitada.');
        }

        if (! class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            throw new \RuntimeException('PhpSpreadsheet não instalado. Habilite ext-zip e instale phpoffice/phpspreadsheet.');
        }

        // Implementação futura: abrir XLSX, substituir placeholders em células e salvar.
        throw new \RuntimeException('Geração de XLSX por template ainda não implementada neste ambiente.');
    }

    private function resolvePath(string $path): string
    {
        if (str_starts_with($path, 'resources/')) {
            return base_path($path);
        }
        if (! str_contains($path, DIRECTORY_SEPARATOR)) {
            // Caminho relativo ao diretório padrão de templates
            $defaultDir = resource_path('reports/templates');

            return $defaultDir . DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }
}
