<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Caminho do JSON (ajuste se necessário)
        $path = database_path('seeders/data/user_profiles.json');

        if (! File::exists($path)) {
            $this->command->error("❌ Arquivo JSON não encontrado em: {$path}");

            return;
        }

        $json = File::get($path);
        $pessoas = json_decode($json, true);

        if (! $pessoas) {
            $this->command->error('❌ Falha ao ler o JSON.');

            return;
        }

        $this->command->info('📥 Importando ' . count($pessoas) . ' perfis fictícios...');

        $i = 1;
        foreach ($pessoas as $pessoa) {

            // Cria um user se não existir (vincula 1:1)
            $user = User::firstOrCreate(
                ['email' => $pessoa['email']],
                [
                    'name' => $pessoa['nome'],
                    'password' => bcrypt($pessoa['senha']),
                    'role_id' => 2, // exemplo: 2 = usuário padrão
                ]
            );

            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nome_completo' => $pessoa['nome'],
                    'cpf' => $pessoa['cpf'],
                    'rg' => $pessoa['rg'] ?? null,
                    'data_nascimento' => $this->parseData($pessoa['data_nasc'] ?? null),
                    'idade' => $pessoa['idade'] ?? null,
                    'sexo' => $pessoa['sexo'] ?? null,
                    'mae' => $pessoa['mae'] ?? null,
                    'pai' => $pessoa['pai'] ?? null,
                    'tipo_sanguineo' => $pessoa['tipo_sanguineo'] ?? null,
                    'altura' => isset($pessoa['altura'])
    ? str_replace(',', '.', $pessoa['altura'])
    : null,
                    'peso' => isset($pessoa['peso'])
                        ? str_replace(',', '.', $pessoa['peso'])
                        : null,
                    'cep' => $pessoa['cep'] ?? null,
                    'endereco' => $pessoa['endereco'] ?? null,
                    'numero' => strval($pessoa['numero'] ?? ''),
                    'bairro' => $pessoa['bairro'] ?? null,
                    'cidade' => $pessoa['cidade'] ?? 'Belém',
                    'estado' => $pessoa['estado'] ?? 'PA',
                    'telefone_fixo' => $pessoa['telefone_fixo'] ?? null,
                    'celular' => $pessoa['celular'] ?? null,
                    'matricula' => 'M' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'cargo' => fake()->randomElement(['Analista', 'Técnico', 'Coordenador', 'Fiscal']),
                    'dre' => fake()->randomElement(['DRE 1', 'DRE 2', 'DRE 3', 'DRE 4']),
                    'lotacao' => fake()->randomElement(['SEDUC-PA', 'Escola Estadual', 'Coordenação Regional']),
                    'foto' => null,
                    'observacoes' => 'Perfil importado via seeder.',
                    'data_atualizacao' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $i++;
        }

        $this->command->info('✅ Perfis importados com sucesso!');
    }

    private function parseData(?string $data)
    {
        if (! $data) {
            return null;
        }
        try {
            return Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
