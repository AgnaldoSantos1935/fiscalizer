<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use OpenAI;

class IAContratoService
{
    public function processarContrato(string $path, string $nome)
    {
        $base64 = base64_encode(Storage::disk('public')->get($path));

        $client = OpenAI::client(config('services.openai.key'));

        $response = $client->chat()->create([
            'model' => 'gpt-4.1',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Você é um especialista em análise contratual, jurídico administrativo e auditoria pública.',
                ],
                [
                    'role' => 'user',
                    'content' => 'Extraia todas as informações estruturadas conforme o JSON oficial do contrato e diga inconsistências.',
                ],
                [
                    'role' => 'user',
                    'content' => "Arquivo base64:\n".$base64,
                ],
            ],
        ]);

        $json = $this->extrairJson($response->choices[0]->message->content);

        return json_decode($json, true);
    }

    private function extrairJson($texto)
    {
        preg_match('/\{(?:[^{}]|(?R))*\}/', $texto, $match);

        return $match[0] ?? '{}';
    }
}
