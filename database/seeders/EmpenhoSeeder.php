<?php

namespace Database\Seeders;

use App\Models\Empenho;
use App\Models\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmpenhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Apoio: garantir pelo menos uma empresa
        $empresa = Empresa::query()->firstOrCreate(
            ['cnpj' => '12.345.678/0001-99'],
            [
                'razao_social' => 'Tech Serviços Ltda',
                'email' => 'contato@techservicos.com.br',
                'telefone' => '(11) 99999-0000',
                'endereco' => 'Av. Principal, 1000',
                'cidade' => 'São Paulo',
                'uf' => 'SP',
                'cep' => '01000-000',
            ]
        );

        // Para cada contrato, criar de 1 a 3 NEs se ainda não houver
        $contratos = DB::table('contratos')->get();
        foreach ($contratos as $contrato) {
            $existingCount = Empenho::query()->where('contrato_id', $contrato->id)->count();
            $target = max(1, 3 - $existingCount);

            for ($i = 0; $i < $target; $i++) {
                $dataLanc = ($contrato->data_inicio_vigencia ?? now()->toDateString());
                $dataLanc = \Carbon\Carbon::parse($dataLanc)->addDays(random_int(0, 90));

                Empenho::query()->create([
                    'numero' => 'NE-' . Str::upper(Str::random(10)),
                    'contrato_id' => $contrato->id,
                    'empresa_id' => $empresa->id,
                    'processo' => $contrato->processo_origem ?? sprintf('%05d/%d', random_int(10000, 99999), now()->year),
                    'programa_trabalho' => 'PT ' . random_int(1000, 9999) . '.' . random_int(1000, 9999),
                    'fonte_recurso' => ['Tesouro', 'Recursos Próprios', 'Convênio'][array_rand(['Tesouro', 'Recursos Próprios', 'Convênio'])],
                    'natureza_despesa' => (string) random_int(339001, 339099),
                    'data_lancamento' => $dataLanc->toDateString(),
                    'valor_total' => 0, // será calculado após criar itens
                ]);
            }
        }
    }
}
