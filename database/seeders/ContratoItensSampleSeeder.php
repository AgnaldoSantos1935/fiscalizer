<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contrato;
use App\Models\ContratoItem;

class ContratoItensSampleSeeder extends Seeder
{
    public function run(): void
    {
        $contratos = Contrato::take(2)->get();

        if ($contratos->isEmpty()) {
            $contrato = Contrato::create([
                'numero' => 'EXEMPLO-001',
                'objeto' => 'Contrato de exemplo para popular itens',
                'valor_global' => 0,
                'situacao' => 'vigente',
            ]);
            $contratos = collect([$contrato]);
        }

        foreach ($contratos as $contrato) {
            if ($contrato->itens()->exists()) {
                continue;
            }

            $amostras = [
                [
                    'descricao_item' => 'Serviço de manutenção',
                    'unidade_medida' => 'h',
                    'quantidade' => 100,
                    'valor_unitario' => 150.00,
                    'tipo_item' => 'servico',
                    'status' => 'ativo',
                ],
                [
                    'descricao_item' => 'Licença de software',
                    'unidade_medida' => 'un',
                    'quantidade' => 50,
                    'valor_unitario' => 200.00,
                    'tipo_item' => 'software',
                    'status' => 'ativo',
                ],
                [
                    'descricao_item' => 'Material de rede',
                    'unidade_medida' => 'un',
                    'quantidade' => 30,
                    'valor_unitario' => 75.50,
                    'tipo_item' => 'material',
                    'status' => 'ativo',
                ],
            ];

            foreach ($amostras as $dados) {
                $contrato->itens()->create($dados);
            }
        }
    }
}

