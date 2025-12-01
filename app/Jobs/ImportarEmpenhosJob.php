<?php

namespace App\Jobs;

use App\Events\EmpenhosImportadosEvent;
use App\Models\Empenho;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImportarEmpenhosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60 * 10;

    public function handle()
    {
        // 1. Baixar CSV do portal
        $url = 'https://api.portaltransparencia.gov/despesas.csv';
        $csv = Http::get($url)->body();

        Storage::put('empenhos/empenhos_raw.csv', $csv);

        // 2. Converter CSV → JSON
        $json = $this->converterCsvParaJson($csv);

        Storage::put('empenhos/empenhos.json', json_encode($json, JSON_PRETTY_PRINT));

        // 3. Importar no banco
        foreach ($json as $item) {
            Empenho::updateOrCreate(
                [
                    'ano' => $item['ano'],
                    'mes' => $item['mes'],
                    'unidade_gestora' => $item['unidade_gestora'],
                    'elemento_despesa' => $item['elemento_despesa'],
                ],
                [
                    'orgao' => $item['orgao'],
                    'programa' => $item['programa'],
                    'valor_empenhado' => $item['valor_empenhado'],
                    'valor_liquidado' => $item['valor_liquidado'],
                    'valor_pago' => $item['valor_pago'],
                ]
            );
        }

        // 4. Disparar evento para atualizar dashboards
        event(new EmpenhosImportadosEvent);
    }

    private function converterCsvParaJson($csv)
    {
        $linhas = explode("\n", $csv);
        $cabecalho = str_getcsv(array_shift($linhas), ';');

        $dados = [];

        foreach ($linhas as $linha) {
            if (trim($linha) === '') {
                continue;
            }

            $valores = str_getcsv($linha, ';');
            $registro = array_combine($cabecalho, $valores);

            $dados[] = [
                'ano' => (int) $registro['Ano'],
                'mes' => (int) $registro['Mes'],
                'unidade_gestora' => $registro['Unidade_Gestora'],
                'orgao' => $registro['Orgao'],
                'programa' => $registro['Programa_Governo'],
                'elemento_despesa' => $registro['Elemento_Despesa'],
                'valor_empenhado' => $this->toFloat($registro['Empenhado']),
                'valor_liquidado' => $this->toFloat($registro['Liquidado']),
                'valor_pago' => $this->toFloat($registro['Pago']),
            ];
        }

        return $dados;
    }

    private function toFloat($val)
    {
        return floatval(str_replace(['.', ','], ['', '.'], $val));
    }
}
