<?php

namespace Database\Seeders;

use App\Models\Empenho;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class EmpenhosSeeder extends Seeder
{
    public function run(): void
    {
        $json = Storage::disk('local')->get('data/empenhos.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Empenho::updateOrCreate(
                [
                    'ano' => $item['ano'],
                    'mes' => $item['mes'],
                    'elemento_despesa' => $item['elemento_despesa'],
                    'unidade_gestora' => $item['unidade_gestora'],
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
    }
}
