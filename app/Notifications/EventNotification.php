<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
    use Queueable;

    protected string $codigo;

    protected array $data;

    protected $subject;

    public function __construct(string $codigo, array $data = [], $subject = null)
    {
        $this->codigo = $codigo;
        $this->data = $data;
        $this->subject = $subject;
    }

    public function via($notifiable)
    {
        // Permite canais dinâmicos configurados por evento
        $channels = $this->data['channels'] ?? ['database'];

        return is_array($channels) ? $channels : ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'codigo' => $this->codigo,
            'titulo' => $this->data['titulo'] ?? 'Evento no Fiscalizer',
            'mensagem' => $this->data['mensagem'] ?? null,
            'dados' => $this->data,
            'subject' => $this->normalizeSubject(),
        ];
    }

    protected function normalizeSubject(): ?array
    {
        if (! $this->subject) {
            return null;
        }
        try {
            // Attempt to extract model info when an Eloquent model is provided
            if (is_object($this->subject) && method_exists($this->subject, 'getKey')) {
                return [
                    'type' => get_class($this->subject),
                    'id' => $this->subject->getKey(),
                ];
            }
        } catch (\Throwable $e) {
            // swallow
        }

        return is_array($this->subject) ? $this->subject : ['value' => $this->subject];
    }
}
