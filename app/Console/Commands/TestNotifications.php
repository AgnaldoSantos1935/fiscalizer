<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestNotifications extends Command
{
    protected $signature = 'notificacoes:testar {--contrato_id=} {--medicao_id=}';

    protected $description = 'Dispara notificações de teste para verificar o sistema.';

    public function handle(): int
    {
        $contrato = null;
        $medicao = null;

        // Resolve contrato e medição caso IDs tenham sido fornecidos
        $contratoId = $this->option('contrato_id');
        if ($contratoId) {
            $contrato = Contrato::find($contratoId);
        } else {
            $contrato = Contrato::first();
        }

        $medicaoId = $this->option('medicao_id');
        if ($medicaoId) {
            $medicao = Medicao::find($medicaoId);
        } else {
            $medicao = Medicao::with('contrato')->first();
        }

        // Evento de contrato (sem subject => notifica todos via RBAC)
        notify_event('notificacoes.contratos.contrato_criado', [
            'titulo' => 'Teste: Contrato criado',
            'mensagem' => $contrato ? "Contrato {$contrato->numero} criado (teste)." : 'Contrato criado (teste) sem contexto.',
        ], $contrato);

        // Evento de medição criada (se existir)
        if ($medicao) {
            notify_event('notificacoes.medicoes.medicao_criada', [
                'titulo' => 'Teste: Medição criada',
                'mensagem' => "Medição {$medicao->id} criada para contrato {$medicao->contrato->numero} (teste).",
            ], $medicao);
        }

        // Evento de medição contestada (se existir)
        if ($medicao) {
            notify_event('notificacoes.medicoes.medicao_contestada', [
                'titulo' => 'Teste: Medição contestada',
                'mensagem' => "Medição {$medicao->id} contestada para contrato {$medicao->contrato->numero} (teste).",
            ], $medicao);
        }

        // Garantia: envia uma notificação direta ao primeiro usuário para validação visual
        $user = User::first();
        if ($user) {
            NotificationService::enviar($user, 'Ping de teste', 'Verifique o sino de notificações no topo.');
            $this->line("Notificação direta enviada ao usuário #{$user->id} ({$user->name}).");
        } else {
            $this->warn('Nenhum usuário encontrado para notificação direta.');
        }

        $this->info('Notificações de teste disparadas.');

        return self::SUCCESS;
    }
}
