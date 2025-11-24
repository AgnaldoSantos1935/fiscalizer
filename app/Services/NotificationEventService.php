<?php

namespace App\Services;

use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\NotificationEvent;
use App\Models\Pessoa;
use App\Models\Role;
use App\Models\User;
use App\Notifications\EventNotification;

class NotificationEventService
{
    /**
     * Dispara notificação para todos usuários com permissão (RBAC) para o código informado.
     * O código deve seguir o padrão: "notificacoes.<dominio>.<evento>".
     * Ex: notificacoes.contratos.contrato_criado
     */
    public function notify(string $codigo, array $data = [], $subject = null): void
    {
        // Usa definição de evento do config, se existir
        // Preferência por definição persistida em banco; fallback no config
        $dbDef = NotificationEvent::where('codigo', $codigo)->first();
        $def = $dbDef ? [
            'title' => $dbDef->title,
            'message' => $dbDef->message,
            'channels' => $dbDef->channels ?? ['database'],
            'enabled' => $dbDef->enabled,
            'priority' => $dbDef->priority ?? 'normal',
            'recipient_scope' => $dbDef->recipient_scope ?? 'intersection',
            'recipient_roles' => $dbDef->recipient_roles ?? [],
            'recipient_users' => $dbDef->recipient_users ?? [],
            'should_generate' => $dbDef->should_generate ?? true,
            'rules' => $dbDef->rules ?? null,
        ] : (config("notification_events.events.$codigo") ?? null);

        if ($def && (! ($def['enabled'] ?? true))) {
            // Evento desabilitado via config
            return;
        }

        if ($def && ($def['should_generate'] ?? true) === false) {
            // Configuração indica que não deve gerar notificações para este evento
            return;
        }

        // Monta payload com templates (somente se não fornecidos em $data)
        $payload = $data;
        if ($def) {
            // Contexto básico vindo de $data
            $ctx = $data;
            // Enriquecimento opcional com subject->toArray()
            try {
                if (is_object($subject) && method_exists($subject, 'toArray')) {
                    $ctx = array_merge($subject->toArray(), $ctx);
                }
            } catch (\Throwable $e) {
                // ignora
            }

            $payload['titulo'] = $payload['titulo'] ?? $this->applyTemplate(($def['title'] ?? 'Evento'), $ctx);
            $payload['mensagem'] = $payload['mensagem'] ?? $this->applyTemplate(($def['message'] ?? ''), $ctx);
            $payload['channels'] = $payload['channels'] ?? ($def['channels'] ?? ['database']);
            $payload['priority'] = $payload['priority'] ?? ($def['priority'] ?? 'normal');
        }

        // Carrega roles com usuários e ações; usa hasAction (suporta curingas) para verificar permissão
        $roles = Role::with(['users', 'actions'])->get();
        $rbacRecipients = collect();

        foreach ($roles as $role) {
            if ($role->hasAction($codigo)) {
                foreach ($role->users as $user) {
                    $rbacRecipients->push($user);
                }
            }
        }

        $rbacRecipients = $rbacRecipients->unique('id');

        // Resolve destinatários adicionais com base no contexto (ex.: usuários vinculados ao contrato)
        $contextRecipients = $this->resolveContextRecipients($codigo, $subject);

        // Destinatários por papéis específicos
        $roleRecipients = collect();
        foreach (($def['recipient_roles'] ?? []) as $roleId) {
            try {
                $role = Role::with('users')->find($roleId);
                foreach (($role->users ?? []) as $u) {
                    $roleRecipients->push($u);
                }
            } catch (\Throwable $e) { /* ignore */
            }
        }
        $roleRecipients = $roleRecipients->unique('id');

        // Destinatários por usuários específicos
        $userRecipients = collect();
        foreach (($def['recipient_users'] ?? []) as $userId) {
            try {
                $u = User::find($userId);
                if ($u) {
                    $userRecipients->push($u);
                }
            } catch (\Throwable $e) { /* ignore */
            }
        }
        $userRecipients = $userRecipients->unique('id');

        // Estratégia de seleção de destinatários
        $scope = $def['recipient_scope'] ?? 'intersection';
        if ($scope === 'rbac') {
            $uniqueRecipients = $rbacRecipients;
        } elseif ($scope === 'context') {
            $uniqueRecipients = $contextRecipients;
        } elseif ($scope === 'all') {
            try {
                $uniqueRecipients = User::all();
            } catch (\Throwable $e) {
                $uniqueRecipients = collect();
            }
        } elseif ($scope === 'roles') {
            $uniqueRecipients = $roleRecipients;
        } elseif ($scope === 'users') {
            $uniqueRecipients = $userRecipients;
        } else { // intersection (default)
            $uniqueRecipients = $contextRecipients->isNotEmpty()
                ? $rbacRecipients->filter(fn ($u) => $contextRecipients->contains('id', $u->id))
                : $rbacRecipients;
        }

        foreach ($uniqueRecipients as $user) {
            // Canal padrão Laravel (database) e quaisquer outros configurados
            try {
                $user->notify(new EventNotification($codigo, $payload, $subject));
            } catch (\Throwable $e) {
                // ignora falha de canais não configurados
            }

            // Persistência no modelo UserNotification (UI atual) + push
            try {
                NotificationService::enviar(
                    $user,
                    $payload['titulo'] ?? 'Evento no Fiscalizer',
                    $payload['mensagem'] ?? null,
                    $codigo,
                    $payload['link'] ?? null
                );
            } catch (\Throwable $e) {
                // ignora
            }
        }
    }

    private function applyTemplate(string $tpl, array $ctx): string
    {
        // Substituição simples de placeholders {chave}
        return preg_replace_callback('/\{([a-zA-Z0-9_\.]+)\}/', function ($m) use ($ctx) {
            $key = $m[1];
            // Suporta acesso simples a campos aninhados com dot (ex.: projeto.id)
            $val = $ctx;
            foreach (explode('.', $key) as $seg) {
                if (is_array($val) && array_key_exists($seg, $val)) {
                    $val = $val[$seg];
                } else {
                    $val = null;
                    break;
                }
            }

            return $val !== null ? (string) $val : $m[0];
        }, $tpl);
    }

    /**
     * Resolve destinatários de acordo com o contexto do evento e do subject.
     * Retorna usuários diretamente vinculados ao recurso (ex.: contrato), quando aplicável.
     */
    private function resolveContextRecipients(string $codigo, $subject)
    {
        $recipients = collect();

        // Esperado: notificacoes.<dominio>.<evento>
        $parts = explode('.', $codigo);
        $dominio = $parts[1] ?? null;

        // Eventos de medições: subject deve ser Medicao; destinatários são usuários vinculados ao contrato
        if ($dominio === 'medicoes' && $subject instanceof Medicao) {
            $contrato = $subject->contrato; // relação belongsTo
            if ($contrato instanceof Contrato) {
                // Usuários do pivot contrato_fiscais
                try {
                    foreach ($contrato->fiscais ?? collect() as $user) {
                        if ($user instanceof User) {
                            $recipients->push($user);
                        }
                    }
                } catch (\Throwable $e) {
                    // ignora
                }

                // Campos diretos (Pessoa -> User) incluindo suplentes ativos
                $pessoaIds = [
                    $contrato->fiscal_tecnico_id,
                    $contrato->fiscal_administrativo_id,
                    $contrato->gestor_id,
                ];

                if ($contrato->suplente_fiscal_tecnico_ativo) {
                    $pessoaIds[] = $contrato->suplente_fiscal_tecnico_id;
                }
                if ($contrato->suplente_fiscal_administrativo_ativo) {
                    $pessoaIds[] = $contrato->suplente_fiscal_administrativo_id;
                }

                foreach (array_filter($pessoaIds) as $pid) {
                    try {
                        $p = Pessoa::find($pid);
                        if ($p && $p->user) {
                            $recipients->push($p->user);
                        }
                    } catch (\Throwable $e) {
                        // ignora
                    }
                }
            }
        }

        // Eventos de contratos: subject pode ser Contrato diretamente
        if ($dominio === 'contratos' && $subject instanceof Contrato) {
            $contrato = $subject;
            // Usuários do pivot contrato_fiscais
            try {
                foreach ($contrato->fiscais ?? collect() as $user) {
                    if ($user instanceof User) {
                        $recipients->push($user);
                    }
                }
            } catch (\Throwable $e) {
                // ignora
            }

            // Campos diretos (Pessoa -> User) incluindo suplentes ativos
            $pessoaIds = [
                $contrato->fiscal_tecnico_id,
                $contrato->fiscal_administrativo_id,
                $contrato->gestor_id,
            ];

            if ($contrato->suplente_fiscal_tecnico_ativo) {
                $pessoaIds[] = $contrato->suplente_fiscal_tecnico_id;
            }
            if ($contrato->suplente_fiscal_administrativo_ativo) {
                $pessoaIds[] = $contrato->suplente_fiscal_administrativo_id;
            }

            foreach (array_filter($pessoaIds) as $pid) {
                try {
                    $p = Pessoa::find($pid);
                    if ($p && $p->user) {
                        $recipients->push($p->user);
                    }
                } catch (\Throwable $e) {
                    // ignora
                }
            }
        }

        return $recipients->unique('id');
    }
}
