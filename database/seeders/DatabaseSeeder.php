<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Contrato;
use App\Models\OcorrenciaFiscalizacao;
use App\Models\Projeto;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
             // Gera 50 empresas
              Empresa::factory(50)->create();
Empresa::factory()->create([
    'nome' => 'Empresa Exemplo',
    'endereco' => 'Rua Exemplo, 123',
    'telefone' => '(11) 98765-4321',
    'email' => 'savavava@gmail.com',
]);
               // Gera 100 contratos vinculados a empresas
               Contrato::factory(100)->create();

                 // Gera 100 ocorrências de fiscalização
                  OcorrenciaFiscalizacao::factory(100)->create();
                 // Gera 100 ocorrências de fiscalização
                  Projeto::factory(100)->create();
                  $this->call(EmpresaSeeder::class);


    }
}
