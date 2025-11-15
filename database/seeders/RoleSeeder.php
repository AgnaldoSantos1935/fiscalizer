<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['nome' => 'Administrador', 'descricao' => 'Acesso total ao sistema.'],
            ['nome' => 'Gestor de Contrato', 'descricao' => 'Gerencia contratos e medições.'],
            ['nome' => 'Fiscal', 'descricao' => 'Registra medições e ocorrências de fiscalização.'],
            ['nome' => 'admin', 'descricao' => 'Cadastra, cria usuários e parametriza o sistema.'],
            ['nome' => 'fiscal_administrativo', 'descricao' => 'Responsável por inserir e validar a documentação, vigência, reajustes e etc.'],
            ['nome' => 'fiscal_tecnico', 'descricao' => 'Valida os aspectos técnicos, quantitativos e qualitativos dos serviços.'],
            ['nome' => 'gestor','descricao' => 'Aprovação da documentação e avaliações dos árâmetros técnicos.'],
        ]);
    }
}
