<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles e perfis/usuários
        $this->call(RoleSeeder::class);
        \App\Models\User::updateOrCreate(
            ['email' => 'agnaldosantos1935@gmail.com'],
            [
                'name' => 'agnaldo santos',
                'password' => bcrypt('S@n%tos123'),
                'role_id' => 1,
            ]
        );
        // RBAC: Actions e vínculo Role↔Action
        $this->call(ActionsSeeder::class);
        $this->call(RoleActionsSeeder::class);
        $this->call(UserProfileSeeder::class);

        // Hosts de monitoramento
        $this->call(HostSeeder::class);

        // Projetos de software
        $this->call(ProjetoSoftwareSeeder::class);

        // Pessoas/Servidores
        $this->call(PessoaServidorSeeder::class);

        // Contrato base, empenhos e itens
        $this->call(ContratoSeeder::class);
        // Vinculação de fiscais aos contratos
        $this->call(ContratoFiscalSeeder::class);
        $this->call(EmpenhoSeeder::class);
        $this->call(EmpenhoItemSeeder::class);

        // Demandas e requisitos
        $this->call(DemandaRequisitosSeeder::class);
        $this->call(ProcessoProjetoSeeder::class);
        $this->call(ProcessoMedicaoSeeder::class);
        // Popular tabelas relacionadas ao Termo de Referência
        $this->call(TermoReferenciaSeeder::class);

        // Ordens de Fornecimento (gera ao menos 30 registros)
        $this->call(OrdemFornecimentoSeeder::class);

        // Normas técnicas indexadas para demonstração
        $this->call(NormaTrechoSeeder::class);
    }
}
