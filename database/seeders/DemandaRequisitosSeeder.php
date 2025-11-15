<?php

namespace Database\Seeders;

use App\Models\RequisitoSistema;
use Illuminate\Database\Seeder;
use App\Models\Demanda;


class DemandaRequisitosSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1) Demanda Evolutiva: Novo módulo de relatórios
        |--------------------------------------------------------------------------
        */

        $d1 = Demanda::create([
            'projeto_id'      => 1,
            'sistema_id'      => 2,
            'modulo_id'       => 10,
            'tipo_manutencao' => 'evolutiva',
            'titulo'          => 'Criação do módulo de relatórios avançados',
            'descricao'       => 'Implementação de dashboard e relatórios exportáveis.',
            'data_abertura'   => now()->subDays(10),
            'prioridade'      => 'alta',
            'status'          => 'aberta'
        ]);

        Requisito::insert([
            [
                'demanda_id'   => $d1->id,
                'codigo_interno'=> 'REQ001',
                'titulo'       => 'Criar endpoint para geração de relatórios',
                'descricao'    => 'API REST com filtros de data e usuário.',
                'complexidade' => 'media'
            ],
            [
                'demanda_id'   => $d1->id,
                'codigo_interno'=> 'REQ002',
                'titulo'       => 'Implementar dashboard com gráficos',
                'descricao'    => 'Uso de Chart.js para indicadores.',
                'complexidade' => 'alta'
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2) Demanda Corretiva: Corrigir bug no login
        |--------------------------------------------------------------------------
        */

        $d2 = Demanda::create([
            'projeto_id'      => 1,
            'sistema_id'      => 2,
            'modulo_id'       => 3,
            'tipo_manutencao' => 'corretiva',
            'titulo'          => 'Erro de autenticação no login de usuários',
            'descricao'       => 'Falha intermitente ao validar sessão.',
            'data_abertura'   => now()->subDays(3),
            'prioridade'      => 'critica',
            'status'          => 'aberta'
        ]);

        RequisitoSistema::insert([
            [
                'demanda_id'    => $d2->id,
                'codigo_interno'=> 'REQ003',
                'titulo'        => 'Correção na validação de token',
                'descricao'     => 'Ajuste no middleware de autenticação.',
                'complexidade'  => 'baixa'
            ],
            [
                'demanda_id'    => $d2->id,
                'codigo_interno'=> 'REQ004',
                'titulo'        => 'Ajuste no tempo de expiração da sessão',
                'descricao'     => 'Aumentar TTL e refatorar refresh.',
                'complexidade'  => 'baixa'
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3) Demanda Adaptativa: Alterar layout para RN4321
        |--------------------------------------------------------------------------
        */

        $d3 = Demanda::create([
            'projeto_id'      => 2,
            'sistema_id'      => 4,
            'modulo_id'       => 9,
            'tipo_manutencao' => 'adaptativa',
            'titulo'          => 'Adequação do layout ao padrão RN 4321',
            'descricao'       => 'Atualização de campos obrigatórios.',
            'data_abertura'   => now()->subDays(5),
            'prioridade'      => 'media',
            'status'          => 'em andamento'
        ]);

        RequisitoSistema::insert([
            [
                'demanda_id'    => $d3->id,
                'codigo_interno'=> 'REQ005',
                'titulo'        => 'Adaptar layout XML ao novo schema',
                'descricao'     => 'Validar campos obrigatórios e opcionalidade.',
                'complexidade'  => 'alta'
            ]
        ]);

    }
}
