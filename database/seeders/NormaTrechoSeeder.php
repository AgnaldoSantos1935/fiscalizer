<?php

namespace Database\Seeders;

use App\Models\NormaTrecho;
use App\Services\EmbeddingsService;
use Illuminate\Database\Seeder;

class NormaTrechoSeeder extends Seeder
{
    public function run(): void
    {
        $emb = app(EmbeddingsService::class);

        $exemplos = [
            [
                'fonte' => 'NBR 14565',
                'referencia' => 'seção 5.4.2',
                'idioma' => 'pt-BR',
                'trecho_texto' => 'A categoria 6 permite transmissão a 1 Gbit/s em enlaces de até 100 m.',
                'tags' => ['cabeamento', 'cat6', 'gigabit'],
            ],
            [
                'fonte' => 'ISO/IEC 11801',
                'referencia' => 'seção 6.2',
                'idioma' => 'pt-BR',
                'trecho_texto' => 'O comprimento máximo do enlace permanente do cabeamento horizontal é de 90 metros, com cordões de conexão totalizando até 10 metros.',
                'tags' => ['cabeamento', 'distancia', 'horizontal'],
            ],
            [
                'fonte' => 'IEEE 802.11ax',
                'referencia' => 'seção 4.3.1',
                'idioma' => 'pt-BR',
                'trecho_texto' => 'O padrão 802.11ax foi projetado para ambientes de alta densidade, com maior capacidade, menor latência e melhor eficiência espectral em comparação a 802.11n/ac.',
                'tags' => ['wifi6', 'densidade', 'eficiencia'],
            ],
        ];

        foreach ($exemplos as $ordem => $e) {
            $vec = $emb->embed($e['trecho_texto']);
            NormaTrecho::updateOrCreate([
                'fonte' => $e['fonte'],
                'referencia' => $e['referencia'],
                'trecho_texto' => $e['trecho_texto'],
            ], [
                'idioma' => $e['idioma'],
                'tags' => $e['tags'],
                'trecho_ordem' => $ordem + 1,
                'embedding' => $vec,
            ]);
        }
    }
}
