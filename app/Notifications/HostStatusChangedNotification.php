<?php

// app/Notifications/HostStatusChangedNotification.php
namespace App\Notifications;

use App\Models\Host;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class HostStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Host $host,
        public bool $online,
        public ?string $erro = null
    ) {}

    public function via($notifiable)
    {
        return ['mail']; // depois você pode incluir 'database', 'slack', canal WhatsApp custom etc.
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('['.($this->online ? 'ONLINE' : 'OFFLINE').'] '.$this->host->nome_conexao)
            ->line('Host: '.$this->host->nome_conexao)
            ->line('Alvo: '.$this->host->host_alvo)
            ->line('Status: '.($this->online ? 'ONLINE' : 'OFFLINE'))
            ->when(!$this->online && $this->erro, fn ($mail) =>
                $mail->line('Erro: '.$this->erro)
            )
            ->line('Fiscalizer – Monitoramento automático.');
    }
}

