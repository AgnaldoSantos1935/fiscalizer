<?php

namespace Database\Seeders;

use App\Models\Demanda;
use App\Models\Projeto;
use App\Models\RequisitoSistema;
use Illuminate\Database\Seeder;

class DemandaRequisitosSeeder extends Seeder
{
    public function run(): void
    {
        // Garantir projetos mínimos para respeitar a FK de demandas
        $projeto1 = Projeto::firstOrCreate([
            'codigo' => 'PRJ-SEED-001',
        ], [
            'contrato_id' => null,
            'titulo' => 'Projeto Seed 001',
            'sistema' => 'Sistema X',
            'modulo' => 'Módulo A',
            'status' => 'planejado',
        ]);

        $projeto2 = Projeto::firstOrCreate([
            'codigo' => 'PRJ-SEED-002',
        ], [
            'contrato_id' => null,
            'titulo' => 'Projeto Seed 002',
            'sistema' => 'Sistema Y',
            'modulo' => 'Módulo B',
            'status' => 'planejado',
        ]);
        /*
        |--------------------------------------------------------------------------
        | 1) Demanda Evolutiva: Novo módulo de relatórios
        |--------------------------------------------------------------------------
        */

        $d1 = Demanda::create([
            'projeto_id' => $projeto1->id,
            'sistema_id' => 2,
            'modulo_id' => 10,
            'tipo_manutencao' => 'evolutiva',
            'titulo' => 'Criação do módulo de relatórios avançados',
            'descricao' => 'Implementação de dashboard e relatórios exportáveis.',
            'data_abertura' => now()->subDays(10),
            'prioridade' => 'alta',
            'status' => 'aberta',
        ]);

        RequisitoSistema::insert([
            [
                'demanda_id' => $d1->id,
                'codigo_interno' => 'REQ001',
                'titulo' => 'Criar endpoint para geração de relatórios',
                'descricao' => 'API REST com filtros de data e usuário.',
                'complexidade' => 'media',
            ],
            [
                'demanda_id' => $d1->id,
                'codigo_interno' => 'REQ002',
                'titulo' => 'Implementar dashboard com gráficos',
                'descricao' => 'Uso de Chart.js para indicadores.',
                'complexidade' => 'alta',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2) Demanda Corretiva: Corrigir bug no login
        |--------------------------------------------------------------------------
        */

        $d2 = Demanda::create([
            'projeto_id' => $projeto1->id,
            'sistema_id' => 2,
            'modulo_id' => 3,
            'tipo_manutencao' => 'corretiva',
            'titulo' => 'Erro de autenticação no login de usuários',
            'descricao' => 'Falha intermitente ao validar sessão.',
            'data_abertura' => now()->subDays(3),
            'prioridade' => 'critica',
            'status' => 'aberta',
        ]);

        RequisitoSistema::insert([
            [
                'demanda_id' => $d2->id,
                'codigo_interno' => 'REQ003',
                'titulo' => 'Correção na validação de token',
                'descricao' => 'Ajuste no middleware de autenticação.',
                'complexidade' => 'baixa',
            ],
            [
                'demanda_id' => $d2->id,
                'codigo_interno' => 'REQ004',
                'titulo' => 'Ajuste no tempo de expiração da sessão',
                'descricao' => 'Aumentar TTL e refatorar refresh.',
                'complexidade' => 'baixa',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3) Demanda Adaptativa: Alterar layout para RN4321
        |--------------------------------------------------------------------------
        */

        $d3 = Demanda::create([
            'projeto_id' => $projeto2->id,
            'sistema_id' => 4,
            'modulo_id' => 9,
            'tipo_manutencao' => 'adaptativa',
            'titulo' => 'Adequação do layout ao padrão RN 4321',
            'descricao' => 'Atualização de campos obrigatórios.',
            'data_abertura' => now()->subDays(5),
            'prioridade' => 'media',
            'status' => 'em andamento',
        ]);

        RequisitoSistema::insert([
            [
                'demanda_id' => $d3->id,
                'codigo_interno' => 'REQ005',
                'titulo' => 'Adaptar layout XML ao novo schema',
                'descricao' => 'Validar campos obrigatórios e opcionalidade.',
                'complexidade' => 'alta',
            ],
        ]);

    }
}
