<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\Servidor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PessoaServidorSeeder extends Seeder
{
    public function run(): void
    {
        // Caminho do JSON com os dados
        $path = database_path('seeders/data/pessoas_servidores.json');

        if (! File::exists($path)) {
            $this->command->error("❌ Arquivo JSON não encontrado em: {$path}");

            return;
        }

        $data = json_decode(File::get($path), true);

        if (! $data) {
            $this->command->error('❌ Erro ao ler o JSON: formato inválido.');

            return;
        }

        $this->command->info('📦 Iniciando importação de ' . count($data) . ' pessoas...');

        foreach ($data as $pessoaData) {
            // Criação ou atualização da Pessoa
            $pessoa = Pessoa::updateOrCreate(
                ['cpf' => $pessoaData['cpf']],
                [
                    'nome_completo' => $pessoaData['nome_completo'],
                    'rg' => $pessoaData['rg'] ?? null,
                    'data_nascimento' => $pessoaData['data_nascimento'] ?? null,
                    'sexo' => $pessoaData['sexo'] ?? null,
                    'email' => $pessoaData['email'] ?? null,
                    'telefone' => $pessoaData['telefone'] ?? null,
                    'cep' => $pessoaData['cep'] ?? null,
                    'logradouro' => $pessoaData['logradouro'] ?? null,
                    'numero' => $pessoaData['numero'] ?? null,
                    'bairro' => $pessoaData['bairro'] ?? null,
                    'cidade' => $pessoaData['cidade'] ?? null,
                    'uf' => $pessoaData['uf'] ?? null,
                ]
            );

            // Se existir chave 'servidor', cria o registro vinculado
            if (isset($pessoaData['servidor'])) {
                $servidorData = $pessoaData['servidor'];

                Servidor::updateOrCreate(
                    ['matricula' => $servidorData['matricula']],
                    [
                        'pessoa_id' => $pessoa->id,
                        'cargo' => $servidorData['cargo'] ?? null,
                        'funcao' => $servidorData['funcao'] ?? null,
                        'lotacao' => $servidorData['lotacao'] ?? null,
                        'data_admissao' => $servidorData['data_admissao'] ?? null,
                        'vinculo' => $servidorData['vinculo'] ?? 'efetivo',
                        'situacao' => $servidorData['situacao'] ?? 'ativo',
                        'salario' => $servidorData['salario'] ?? 0,
                    ]
                );

                $this->command->info("✅ Servidor criado: {$pessoa->nome_completo} ({$servidorData['matricula']})");
            } else {
                $this->command->warn("ℹ️ Pessoa sem vínculo funcional: {$pessoa->nome_completo}");
            }
        }

        $this->command->info('🎯 Importação concluída com sucesso!');
    }
}
