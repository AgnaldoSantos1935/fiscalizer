<?php

namespace App\Services;

use App\Models\MedicaoDocumento;
use App\Models\MedicaoNotaFiscal;
use Illuminate\Support\Facades\Http;

class ValidadorNotaFiscalService
{
    public function validar(MedicaoNotaFiscal $nf): MedicaoNotaFiscal
    {
        // 1. Detecta tipo automaticamente
        if (strlen($nf->chave) === 44) {
            return $this->validarNFe($nf);
        } else {
            return $this->validarNFSe($nf);
        }
        if ($dados['emitente'] != $doc->medicao->contrato->empresa->cnpj) {
            return $this->inconsistencia(
                $doc,
                'CNPJ do emitente não corresponde ao CNPJ da empresa contratada.'
            );
        }

        if ($dados['valor'] != $doc->medicao->valor_total) {
            return $this->inconsistencia(
                $doc,
                'Valor da Nota Fiscal (R$ ' . $dados['valor'] . ') é diferente do valor da medição (R$ ' . $doc->medicao->valor_total . ').'
            );
        }
        if ($somaPlanilha != $medicao->valor_total) {
            return $this->inconsistencia(
                $doc,
                "Somatório da planilha (R$ $somaPlanilha) não confere com o valor da medição (R$ " . $medicao->valor_total . ').'
            );
        }
        if ($dataValidade < today()) {
            return $this->inconsistencia(
                $doc,
                'Certidão vencida em ' . $dataValidade->format('d/m/Y') . '.'
            );
        }
        if (! $autenticidade) {
            return $this->inconsistencia(
                $doc,
                'Código de autenticidade da NFSe não é válido no portal do município.'
            );
        }
        if ($quantidadeFotos < 3) {
            return $this->inconsistencia(
                $doc,
                'Relatório de execução possui menos de 3 evidências fotográficas.'
            );
        }

    }

    private function validarNFe(MedicaoNotaFiscal $nf): MedicaoNotaFiscal
    {
        $url = 'https://api.sefa.pa.gov.br/nfe/consulta/' . $nf->chave;

        $response = Http::get($url);

        if (! $response->successful()) {
            return $this->erro($nf, 'Falha na consulta à SEFA/PA.');
        }

        $data = $response->json();

        // --- Validações automáticas ---
        if ($data['status'] != 'Autorizado') {
            return $this->invalido($nf, 'Nota fiscal não está autorizada.');
        }

        if ($data['cnpj_emitente'] != $nf->cnpj_prestador) {
            return $this->invalido($nf, 'CNPJ do prestador não confere.');
        }

        if ($nf->medicao->contrato->empresa->cnpj != $data['cnpj_emitente']) {
            return $this->invalido($nf, 'NF não pertence à empresa do contrato.');
        }

        if ($data['valor_total'] != $nf->valor) {
            return $this->invalido($nf, 'Valor da NF difere do valor da medição.');
        }

        // Se passou por todas → VÁLIDA
        return $this->valida($nf, $data);
    }

    private function validarNFSe(MedicaoNotaFiscal $nf): MedicaoNotaFiscal
    {
        $url = 'https://api.belem.pa.gov.br/nfse/validar/' . $nf->numero . '/' . $nf->cnpj_prestador;

        $response = Http::get($url);

        if (! $response->successful()) {
            return $this->erro($nf, 'Erro na consulta da NFSe.');
        }

        $data = $response->json();

        if ($data['autenticidade'] != true) {
            return $this->invalido($nf, 'NFSe não é autêntica.');
        }

        return $this->valida($nf, $data);
    }

    // Auxiliares ---------------------------------------

    private function valida(MedicaoNotaFiscal $nf, array $data)
    {
        $nf->update([
            'status' => 'valido',
            'mensagem' => 'Nota fiscal validada com sucesso.',
            'retorno_api' => $data,
        ]);

        return $nf;
    }

    private function invalido(MedicaoNotaFiscal $nf, string $motivo)
    {
        $nf->update([
            'status' => 'invalido',
            'mensagem' => $motivo,
        ]);

        return $nf;
    }

    private function erro(MedicaoNotaFiscal $nf, string $motivo)
    {
        $nf->update([
            'status' => 'erro',
            'mensagem' => $motivo,
        ]);

        return $nf;
    }

    private function inconsistencia(MedicaoDocumento $doc, string $motivo)
    {
        $doc->update([
            'status' => 'invalido',
            'mensagem' => $motivo,
        ]);

        // também salva no log da medição
        $doc->medicao->logs()->create([
            'acao' => 'inconsistencia',
            'mensagem' => $motivo,
        ]);

        return $doc;
    }
}
