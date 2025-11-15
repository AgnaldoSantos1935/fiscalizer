<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Processo;
use App\Models\ProcessoEtapa;
use App\Models\ProcessoFluxo;

class ProcessoMedicaoSeeder extends Seeder
{
    public function run(): void
    {
        $processo = Processo::create([
            'nome' => 'Medição Interna de Serviços',
            'codigo' => 'MEDICAO_INTERNA',
            'descricao' => 'Processo interno de medição, análise técnica, validação e aprovação.',
            'versao' => '2.0',
            'ativo' => true,
        ]);

        $etapasData = [
            [
                'nome' => 'Anexar Documentos da Medição',
                'tipo' => 'inicio',
                'ordem' => 1,
                'responsavel_tipo' => 'fiscal_administrativo'
            ],
            [
                'nome' => 'Análise Técnica',
                'tipo' => 'execucao',
                'ordem' => 2,
                'responsavel_tipo' => 'fiscal_tecnico'
            ],
            [
                'nome' => 'Validação Administrativa',
                'tipo' => 'execucao',
                'ordem' => 3,
                'responsavel_tipo' => 'fiscal_administrativo'
            ],
            [
                'nome' => 'Aprovação do Gestor',
                'tipo' => 'aprovacao',
                'ordem' => 4,
                'responsavel_tipo' => 'gestor'
            ],
            [
                'nome' => 'Geração do Boletim e Atesto',
                'tipo' => 'validacao',
                'ordem' => 5,
                'responsavel_tipo' => 'sistema'
            ],
            [
                'nome' => 'Envio ao Processo Eletrônico',
                'tipo' => 'fim',
                'ordem' => 6,
                'responsavel_tipo' => 'sistema'
            ],
        ];

        $etapas = [];

        foreach ($etapasData as $e) {
            $etapas[$e['ordem']] = ProcessoEtapa::create([
                'processo_id'     => $processo->id,
                'nome'            => $e['nome'],
                'ordem'           => $e['ordem'],
                'tipo'            => $e['tipo'],
                'responsavel_tipo'=> $e['responsavel_tipo'],
                'prazo_horas'     => 72,
            ]);
        }

        // fluxo 1 → 2 → 3 → 4 → 5 → 6
        for ($i = 1; $i < count($etapas); $i++) {
            ProcessoFluxo::create([
                'processo_id'      => $processo->id,
                'etapa_origem_id'  => $etapas[$i]->id,
                'etapa_destino_id' => $etapas[$i + 1]->id,
            ]);
        }
    }
}
