<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nome' => 'Administrador', 'descricao' => 'Acesso total ao sistema.'],
            ['nome' => 'Gestor de Contrato', 'descricao' => 'Gerencia contratos e medições.'],
            ['nome' => 'Fiscal', 'descricao' => 'Registra medições e ocorrências de fiscalização.'],
            ['nome' => 'admin', 'descricao' => 'Cadastra, cria usuários e parametriza o sistema.'],
            ['nome' => 'fiscal_administrativo', 'descricao' => 'Responsável por inserir e validar a documentação, vigência, reajustes e etc.'],
            ['nome' => 'fiscal_tecnico', 'descricao' => 'Valida os aspectos técnicos, quantitativos e qualitativos dos serviços.'],
            ['nome' => 'gestor', 'descricao' => 'Aprovação da documentação e avaliações dos árâmetros técnicos.'],
        ];

        // Evitar duplicações mantendo unicidade por 'nome'
        Role::upsert($rows, ['nome'], ['descricao']);
    }
}
