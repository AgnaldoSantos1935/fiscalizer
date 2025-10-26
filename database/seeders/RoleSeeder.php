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
        ]);
    }
}
