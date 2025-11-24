<?php

namespace App\Console\Commands;

use App\Models\Action;
use Illuminate\Console\Command;

class SyncNotificationActions extends Command
{
    protected $signature = 'notificacoes:sync {--wildcards : Criar também curingas por domínio}';

    protected $description = 'Sincroniza Actions para eventos de notificação definidos em config/notification_events.php';

    public function handle(): int
    {
        $events = config('notification_events.events') ?? [];
        if (empty($events)) {
            $this->warn('Nenhum evento definido em config/notification_events.php.');

            return self::SUCCESS;
        }

        $criados = 0;
        $existentes = 0;
        $wildcardsCriados = 0;

        foreach ($events as $codigo => $def) {
            $nome = $def['title'] ?? $codigo;
            $descricao = $def['message'] ?? null;

            $action = Action::where('codigo', $codigo)->first();
            if (! $action) {
                Action::create([
                    'codigo' => $codigo,
                    'nome' => $nome,
                    'descricao' => $descricao,
                ]);
                $criados++;
                $this->info("Criado Action: $codigo");
            } else {
                $existentes++;
            }

            if ($this->option('wildcards')) {
                $parts = explode('.', $codigo);
                if (count($parts) >= 3 && $parts[0] === 'notificacoes') {
                    $dominio = $parts[1];
                    $wc = "notificacoes.$dominio.*";
                    if (! Action::where('codigo', $wc)->exists()) {
                        Action::create([
                            'codigo' => $wc,
                            'nome' => "Notificações de $dominio (todas)",
                            'descricao' => 'Wildcard para receber todos eventos do domínio',
                        ]);
                        $wildcardsCriados++;
                        $this->info("Criado Action wildcard: $wc");
                    }
                }
            }
        }

        $this->line("Ações criadas: $criados; já existentes: $existentes; wildcards criados: $wildcardsCriados");

        return self::SUCCESS;
    }
}
