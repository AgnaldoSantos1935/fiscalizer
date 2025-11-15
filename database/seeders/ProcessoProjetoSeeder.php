<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Processo;
use App\Models\ProcessoEtapa;
use App\Models\ProcessoFluxo;

class ProcessoProjetoSeeder extends Seeder
{
    public function run(): void
    {
        $processo = Processo::create([
            'nome' => 'Projeto de Desenvolvimento de Sistema',
            'codigo' => 'PROJ_DEV_SIST',
            'descricao' => 'Fluxo BPM para projetos de desenvolvimento de sistemas, com requisitos, APF/UST, cronograma e equipe.',
            'versao' => '1.0',
            'ativo' => true,
        ]);

        $etapasData = [
            ['nome' => 'Iniciação',                'tipo' => 'inicio',    'ordem' => 1],
            ['nome' => 'Levantamento de Requisitos','tipo' => 'execucao',  'ordem' => 2],
            ['nome' => 'Estimativa APF/UST',       'tipo' => 'validacao', 'ordem' => 3],
            ['nome' => 'Planejamento',             'tipo' => 'execucao',  'ordem' => 4],
            ['nome' => 'Execução',                 'tipo' => 'execucao',  'ordem' => 5],
            ['nome' => 'Homologação',              'tipo' => 'aprovacao', 'ordem' => 6],
            ['nome' => 'Encerramento',             'tipo' => 'fim',       'ordem' => 7],
        ];

        $etapas = [];

        foreach ($etapasData as $e) {
            $etapas[$e['ordem']] = ProcessoEtapa::create([
                'processo_id'     => $processo->id,
                'nome'            => $e['nome'],
                'ordem'           => $e['ordem'],
                'tipo'            => $e['tipo'],
                'prazo_horas'     => 72,
                'responsavel_tipo'=> 'equipe_projeto',
                'checklist'       => null,
                'ativa'           => true,
            ]);
        }

        // Fluxos simples sequência 1 -> 2 -> 3 ...
        for ($i = 1; $i < count($etapas); $i++) {
            ProcessoFluxo::create([
                'processo_id'      => $processo->id,
                'etapa_origem_id'  => $etapas[$i]->id,
                'etapa_destino_id' => $etapas[$i + 1]->id,
                'regra'            => null,
                'acao_automatica'  => null,
            ]);
        }
    }
}
