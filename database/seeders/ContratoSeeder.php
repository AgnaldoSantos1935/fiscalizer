<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContratoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalidades = ['Pregão Eletrônico', 'Dispensa', 'Concorrência', 'Tomada de Preços'];
        $empresas = [
            ['razao' => 'Tech Serviços Ltda', 'cnpj' => '12.345.678/0001-99'],
            ['razao' => 'Innova Sistemas S.A.', 'cnpj' => '98.765.432/0001-10'],
            ['razao' => 'Alpha TI Ltda', 'cnpj' => '01.234.567/0001-88'],
            ['razao' => 'Delta Soft Ltda', 'cnpj' => '45.678.901/0001-55'],
            ['razao' => 'Omega Tech S.A.', 'cnpj' => '33.222.111/0001-44'],
        ];

        $existing = DB::table('contratos')->count();
        $target = 5;
        $toCreate = max(0, $target - $existing);

        for ($i = 0; $i < $toCreate; $i++) {
            $empresa = $empresas[$i % count($empresas)];
            $assinatura = now()->subMonths(random_int(1, 6));
            $inicio = (clone $assinatura)->addDays(random_int(1, 30));
            $fim = (clone $inicio)->addMonths(random_int(6, 24));

            DB::table('contratos')->insert([
                'numero' => 'CT-' . Str::upper(Str::random(6)),
                'processo_origem' => sprintf('%05d/%d', random_int(10000, 99999), now()->year),
                'modalidade' => $modalidades[array_rand($modalidades)],
                'objeto' => 'Prestação de serviços de desenvolvimento e manutenção de software.',
                'objeto_resumido' => 'Serviços de desenvolvimento e manutenção',
                'valor_global' => random_int(600, 1800) * 1000,
                'valor_mensal' => random_int(50, 200) * 1000,
                'quantidade_meses' => random_int(6, 24),
                'data_assinatura' => $assinatura->toDateString(),
                'data_inicio_vigencia' => $inicio->toDateString(),
                'data_fim_vigencia' => $fim->toDateString(),
                'empresa_razao_social' => $empresa['razao'],
                'empresa_cnpj' => $empresa['cnpj'],
                'empresa_endereco' => 'Av. Principal, ' . random_int(100, 9999),
                'empresa_representante' => 'Representante ' . Str::upper(Str::random(3)),
                'empresa_contato' => sprintf('(11) 9%04d-%04d', random_int(1000, 9999), random_int(1000, 9999)),
                'empresa_email' => 'contato@' . Str::lower(Str::slug($empresa['razao'])) . '.com.br',
                'fiscal_tecnico' => 'Fiscal Técnico ' . Str::upper(Str::random(3)),
                'fiscal_administrativo' => 'Fiscal Administrativo ' . Str::upper(Str::random(3)),
                'gestor' => 'Gestor ' . Str::upper(Str::random(3)),
                'risco_score' => random_int(1, 100),
                'risco_nivel' => ['baixo', 'medio', 'alto'][array_rand(['baixo', 'medio', 'alto'])],
                'risco_detalhes_json' => json_encode(['observacoes' => 'Avaliação automatizada.']),
                'obrigacoes_contratada' => json_encode(['entregas_mensais' => true]),
                'obrigacoes_contratante' => json_encode(['homologacao' => true]),
                'itens_fornecimento' => json_encode(['horas_engenharia_software' => random_int(120, 320)]),
                'anexos_detectados' => json_encode(['edital.pdf', 'proposta.pdf']),
                'clausulas' => json_encode(['multas' => 'Até 10% por descumprimento']),
                'riscos_detectados' => json_encode([]),
                'status' => 'Ativo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
