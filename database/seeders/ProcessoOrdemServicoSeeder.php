<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Processo;
use App\Models\ProcessoEtapa;
use App\Models\ProcessoFluxo;

class ProcessoOrdemServicoSeeder extends Seeder
{
    public function run(): void
    {
        $processo = Processo::create([
            'nome'        => 'Fluxo de Ordem de Serviço – Fábrica de Software',
            'codigo'      => 'OS_FABRICA_SW',
            'descricao'   => 'Fluxo: DETEC registra demanda → empresa envia documento → sistema valida → OS emitida.',
            'versao'      => '1.0',
            'ativo'       => true,
        ]);

        $etapasData = [
            [
                'nome'            => 'Registro da Demanda pela DETEC',
                'tipo'            => 'inicio',
                'ordem'           => 1,
                'responsavel_tipo'=> 'fiscal_administrativo'
            ],
            [
                'nome'            => 'Envio do Pedido à Empresa',
                'tipo'            => 'execucao',
                'ordem'           => 2,
                'responsavel_tipo'=> 'sistema'
            ],
            [
                'nome'            => 'Recebimento do Documento Técnico',
                'tipo'            => 'execucao',
                'ordem'           => 3,
                'responsavel_tipo'=> 'empresa'
            ],
            [
                'nome'            => 'Validação Automática (IA) + DETEC',
                'tipo'            => 'validacao',
                'ordem'           => 4,
                'responsavel_tipo'=> 'fiscal_tecnico'
            ],
            [
                'nome'            => 'Emissão da Ordem de Serviço',
                'tipo'            => 'aprovacao',
                'ordem'           => 5,
                'responsavel_tipo'=> 'gestor'
            ],
            [
                'nome'            => 'Envio da OS à Empresa',
                'tipo'            => 'fim',
                'ordem'           => 6,
                'responsavel_tipo'=> 'sistema'
            ],
        ];

        $etapas = [];
        foreach ($etapasData as $e) {
            $etapas[$e['ordem']] = ProcessoEtapa::create([
                'processo_id'     => $processo->id,
                'nome'            => $e['nome'],
                'tipo'            => $e['tipo'],
                'ordem'           => $e['ordem'],
                'responsavel_tipo'=> $e['responsavel_tipo'],
                'prazo_horas'     => 72,
            ]);
        }

        for ($i = 1; $i < count($etapas); $i++) {
            ProcessoFluxo::create([
                'processo_id'      => $processo->id,
                'etapa_origem_id'  => $etapas[$i]->id,
                'etapa_destino_id' => $etapas[$i + 1]->id,
            ]);
        }
    }
}
