<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SimilarityAiService
{
    protected string $endpoint;

    protected string $apiKey;

    public function __construct()
    {
        $this->endpoint = config('services.ai_similarity.endpoint');
        $this->apiKey = config('services.ai_similarity.key');
    }

    /**
     * Retorna um score de similaridade (0–1) entre dois textos.
     */
    public function similarity(string $textoA, string $textoB): float
    {
        // Exemplo genérico: endpoint que calcula embeddings e cosinus
        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, [
                'text_a' => $textoA,
                'text_b' => $textoB,
            ]);

        if (! $response->successful()) {
            return 0.0;
        }

        return (float) ($response->json()['similarity'] ?? 0.0);
    }
}
