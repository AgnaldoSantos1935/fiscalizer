<?php

namespace Database\Seeders;

use App\Models\Empenho;
use App\Models\OrdemFornecimento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrdemFornecimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $target = 30; // mínimo de ordens de fornecimento

        $existing = OrdemFornecimento::query()->count();
        $remaining = max(0, $target - $existing);
        if ($remaining === 0) {
            return; // já há registros suficientes
        }

        // Buscar empenhos com seus contratos para vincular as OFs
        $empenhos = Empenho::query()
            ->select(['id', 'contrato_id', 'numero', 'processo'])
            ->with(['contrato' => function ($q) {
                $q->select([
                    'id', 'numero', 'processo_origem', 'modalidade', 'objeto_resumido',
                    'data_inicio_vigencia', 'data_fim_vigencia',
                    'empresa_razao_social', 'empresa_cnpj', 'empresa_endereco', 'empresa_representante', 'empresa_contato',
                    'gestor', 'fiscal_administrativo', 'fiscal_tecnico',
                ]);
            }])
            ->get();

        if ($empenhos->isEmpty()) {
            // Sem empenhos, não há como vincular OFs; sair silenciosamente
            return;
        }

        $perEmpenho = (int) ceil($remaining / $empenhos->count());
        $created = 0;
        $year = now()->year;

        foreach ($empenhos as $empenho) {
            if ($created >= $remaining) {
                break;
            }

            $contrato = $empenho->contrato;
            $orgao = config('app.name');

            for ($i = 0; $i < $perEmpenho && $created < $remaining; $i++) {
                $seq = str_pad((string) (OrdemFornecimento::max('id') + 1 + $i), 4, '0', STR_PAD_LEFT);

                // Gerar itens aleatórios
                $itens = [];
                $numItens = random_int(2, 5);
                for ($k = 0; $k < $numItens; $k++) {
                    $qtd = random_int(1, 20);
                    $unit = random_int(50, 1500);
                    $itens[] = [
                        'descricao' => 'Item ' . ($k + 1) . ' - ' . Str::upper(Str::random(6)),
                        'quantidade' => $qtd,
                        'valor_unitario' => $unit,
                        'valor_total' => $qtd * $unit,
                    ];
                }

                $vigIni = $contrato->data_inicio_vigencia ?? now()->toDateString();
                $vigFim = $contrato->data_fim_vigencia ?? now()->addMonths(12)->toDateString();

                OrdemFornecimento::query()->create([
                    'contrato_id' => $empenho->contrato_id,
                    'empenho_id' => $empenho->id,

                    'numero_of' => 'OF-' . $seq,
                    'ano_of' => $year,
                    'data_emissao' => now()->subDays(random_int(0, 20))->toDateString(),
                    'arquivo_pdf' => null,

                    'orgao_entidade' => $orgao,
                    'unidade_requisitante' => 'Unidade ' . Str::upper(Str::random(3)),
                    'cnpj_orgao' => sprintf('%02d.%03d.%03d/%04d-%02d', random_int(10, 99), random_int(100, 999), random_int(100, 999), random_int(1000, 9999), random_int(10, 99)),

                    'contrato_numero' => $contrato->numero ?? ('CT-' . Str::upper(Str::random(6))),
                    'processo_contratacao' => $contrato->processo_origem ?? ($empenho->processo ?? sprintf('%05d/%d', random_int(10000, 99999), $year)),
                    'modalidade' => $contrato->modalidade ?? ['Pregão Eletrônico', 'Dispensa', 'Concorrência'][array_rand(['Pregão Eletrônico', 'Dispensa', 'Concorrência'])],
                    'vigencia_inicio' => $vigIni,
                    'vigencia_fim' => $vigFim,
                    'fundamentacao_legal' => 'Lei nº 14.133/2021 e demais normas aplicáveis.',

                    'contratada_razao_social' => $contrato->empresa_razao_social ?? 'Empresa ' . Str::upper(Str::random(5)) . ' Ltda',
                    'contratada_cnpj' => $contrato->empresa_cnpj ?? sprintf('%02d.%03d.%03d/%04d-%02d', random_int(10, 99), random_int(100, 999), random_int(100, 999), random_int(1000, 9999), random_int(10, 99)),
                    'contratada_endereco' => $contrato->empresa_endereco ?? ('Rua Principal, ' . random_int(100, 9999)),
                    'contratada_representante' => $contrato->empresa_representante ?? ('Representante ' . Str::upper(Str::random(3))),
                    'contratada_contato' => $contrato->empresa_contato ?? sprintf('(11) 9%04d-%04d', random_int(1000, 9999), random_int(1000, 9999)),

                    'prazo_entrega_dias' => random_int(5, 45),
                    'local_entrega' => 'Almoxarifado Central',
                    'horario_entrega' => '08h às 17h (dias úteis)',

                    'recebimento_condicoes' => 'Recebimento condicionado à conferência e nota fiscal.',
                    'obrigacoes_contratada' => 'Entrega conforme especificações e prazos estabelecidos.',
                    'obrigacoes_administracao' => 'Disponibilizar local e equipe para recebimento.',
                    'sancoes' => 'Multas e sanções conforme cláusulas contratuais.',

                    'autoridade_nome' => 'Autoridade Requisitante',
                    'autoridade_cargo' => 'Diretor(a) de Compras',
                    'gestor_nome' => $contrato->gestor ?? 'Gestor ' . Str::upper(Str::random(3)),
                    'gestor_portaria' => 'PORTARIA GESTOR ' . random_int(100, 999) . '/' . $year,
                    'fiscal_nome' => ($contrato->fiscal_administrativo ?? $contrato->fiscal_tecnico ?? 'Fiscal ' . Str::upper(Str::random(3))),
                    'fiscal_portaria' => 'PORTARIA FISCAL ' . random_int(100, 999) . '/' . $year,

                    'assinaturas_json' => json_encode([
                        'autoridade' => ['nome' => 'Autoridade Requisitante'],
                        'gestor' => ['nome' => $contrato->gestor ?? 'Gestor'],
                        'fiscal' => ['nome' => $contrato->fiscal_administrativo ?? $contrato->fiscal_tecnico ?? 'Fiscal'],
                        'contratada' => ['nome' => $contrato->empresa_representante ?? 'Representante'],
                    ]),
                    'assinatura_hash' => Str::uuid()->toString(),
                    'verificacao_url' => url('/verificar/of/' . Str::uuid()->toString()),

                    'itens_json' => json_encode($itens),
                ]);

                $created++;
            }
        }
    }
}
