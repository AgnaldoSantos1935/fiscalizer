<?php

namespace Tests\Feature;

use App\Models\Contrato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpenhoCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_empenho_and_redirects_to_contrato()
    {
        $this->withoutExceptionHandling();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $empresa = \App\Models\Empresa::create([
            'razao_social' => 'Empresa Teste ' . time(),
            'cnpj' => (string) random_int(10000000000000, 99999999999999),
        ]);
        $contrato = Contrato::create([
            'numero' => 'C-' . time(),
            'objeto' => 'Contrato de teste automatizado',
        ]);

        $payload = [
            'numero' => 'TEST-' . time(),
            'empresa_id' => $empresa->id,
            'contrato_id' => $contrato->id,
            'data_lancamento' => now()->toDateString(),
            'itens' => [
                [
                    'descricao' => 'Item teste',
                    'quantidade' => '1,00',
                    'valor_unitario' => '100,00',
                ],
            ],
        ];

        \file_put_contents(base_path('server_log.txt'), 'empenhos.store URL: ' . route('empenhos.store') . PHP_EOL, FILE_APPEND);

        $response = $this->post('/empenhos', $payload);

        $response->assertRedirect(route('contratos.show', $contrato->id));

        $this->assertDatabaseHas('empenhos', [
            'contrato_id' => $contrato->id,
            'empresa_id' => $empresa->id,
            'numero' => $payload['numero'],
            'valor_total' => 100.00,
        ]);
    }
}
