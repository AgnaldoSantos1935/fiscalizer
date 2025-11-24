<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AuditoriaService
{
    public function record(?int $userId, string $action, int $registroId, string $modulo, array $dados = []): void
    {
        // Se existir tabela de auditoria, inserir; caso contrário, logar
        // Implementação mínima: registrar em logs
        Log::info('AUDIT', [
            'user_id' => $userId,
            'action' => $action,
            'registro_id' => $registroId,
            'modulo' => $modulo,
            'dados' => $dados,
        ]);
    }
}
