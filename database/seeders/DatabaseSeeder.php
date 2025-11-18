<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use ProjetoSoftwareSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',

        ]);
        // gerar 30 user_profiles (perfis de pessoas)
         $this->call(UserProfileSeeder::class);
          $this->call(ProjetoSoftwareSeeder::class);
             $this->call([
        PessoaServidorSeeder::class,
    ]);*/
    $this->call(ProcessoProjetoSeeder::class);
    $this->call(ProcessoMedicaoSeeder::class);
    // Popular tabelas relacionadas ao Termo de Referência
    $this->call(TermoReferenciaSeeder::class);
    }
}
