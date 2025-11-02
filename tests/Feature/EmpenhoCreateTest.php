<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Contrato;
use App\Models\Empenho;

class EmpenhoCreateTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_creates_an_empenho_and_redirects_to_contrato()
    {
        // Cria um contrato via factory
        $contrato = Contrato::factory()->create();

        $payload = [
            'contrato_id' => $contrato->id,
            'numero' => 'TEST-'.time(),
            'data_empenho' => now()->toDateString(),
            'valor' => 1234.56,
            'descricao' => 'Teste de empenho',
        ];

        $response = $this->post(route('empenhos.store'), $payload);

        $response->assertRedirect(route('contratos.show', $contrato->id));

        $this->assertDatabaseHas('empenhos', [
            'contrato_id' => $contrato->id,
            'numero' => $payload['numero'],
            'valor' => $payload['valor'],
        ]);
    }
}
