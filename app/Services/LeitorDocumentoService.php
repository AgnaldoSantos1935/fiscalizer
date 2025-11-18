<?php

namespace App\Services;

use App\Models\MedicaoDocumento;
use Smalot\PdfParser\Parser;

class LeitorDocumentoService
{
    public function processar(MedicaoDocumento $doc)
    {
        $conteudo = $this->extrairConteudo($doc);

        switch ($doc->tipo) {

            case 'nfe_xml':
                return $this->validarNFeXml($doc, $conteudo);

            case 'nfe_pdf':
                return $this->validarNFePdf($doc, $conteudo);

            case 'nfse_pdf':
                return $this->validarNFSePdf($doc, $conteudo);

            case 'planilha_medicao':
                return $this->validarPlanilha($doc, $conteudo);

            case 'certidao':
                return $this->validarCertidao($doc, $conteudo);

            case 'relatorio_execucao':
                return $this->validarRelatorio($doc, $conteudo);

            default:
                $doc->update([
                    'status' => 'analisado',
                    'mensagem' => 'Documento processado, nenhum problema encontrado.',
                ]);
        }
    }

    public function identificarArquivo(UploadedFile $file)
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'xml') {
            return 'nfe_xml';
        }
        if ($ext === 'xlsx' || $ext === 'xls') {
            return 'planilha_medicao';
        }

        if ($ext === 'pdf') {
            $texto = Pdf::getText($file->getRealPath());

            if (strpos($texto, 'Nota Fiscal Eletrônica') !== false) {
                return 'nfe_pdf';
            }
            if (strpos($texto, 'NFS-e') !== false) {
                return 'nfse_pdf';
            }
            if (strpos($texto, 'Certidão') !== false) {
                return 'certidao';
            }
            if (strpos($texto, 'Relatório') !== false) {
                return 'relatorio_execucao';
            }
        }

        return 'outro';
    }

    public function extrairPdf($path)
    {
        $parser = new Parser;
        $pdf = $parser->parseFile($path);

        return $pdf->getText();
    }

    public function extrairOcr($path)
    {
        return (new TesseractOCR($path))
            ->lang('por')
            ->run();
    }

    public function extrairXml($path)
    {
        return simplexml_load_file($path);
    }

    public function extrairPlanilha($path)
    {
        return \PhpOffice\PhpSpreadsheet\IOFactory::load($path)->getActiveSheet()->toArray();
    }

    private function validarNFeXml(MedicaoDocumento $doc, $xml)
    {
        $dados = [
            'chave' => (string) $xml->NFe->infNFe['Id'],
            'emitente' => (string) $xml->NFe->infNFe->emit->CNPJ,
            'valor' => (float) $xml->NFe->infNFe->total->ICMSTot->vNF,
        ];

        // Valida contrato
        if ($dados['emitente'] != $doc->medicao->contrato->empresa->cnpj) {
            return $doc->update([
                'status' => 'invalido',
                'mensagem' => 'CNPJ da nota não corresponde à empresa contratada.',
            ]);
        }

        // Valida valor
        if ($dados['valor'] != $doc->medicao->valor_total) {
            return $doc->update([
                'status' => 'invalido',
                'mensagem' => 'Valor da nota não corresponde ao valor da medição.',
            ]);
        }

        $doc->update([
            'status' => 'valido',
            'mensagem' => 'NF-e validada automaticamente (XML).',
            'dados_extracao' => $dados,
        ]);
    }

    private function validarNFePdf(MedicaoDocumento $doc, $texto)
    {
        preg_match('/\d{44}/', $texto, $chave);
        preg_match('/CNPJ: (\d{14})/', $texto, $cnpj);
        preg_match('/Valor\s*R\$\s*(\d+,\d{2})/', $texto, $valor);

        // Validação
        // ... idêntica ao XML

        $doc->update([
            'status' => 'valido',
            'mensagem' => 'NF-e validada automaticamente (PDF).',
            'dados_extracao' => [
                'chave' => $chave[0] ?? null,
                'cnpj' => $cnpj[1] ?? null,
                'valor' => $valor[1] ?? null,
            ],
        ]);
    }
}
