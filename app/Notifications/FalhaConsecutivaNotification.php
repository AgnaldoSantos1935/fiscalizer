<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FalhaConsecutivaNotification extends Notification
{
    use Queueable;

    protected $item;

    protected $falhas;

    public function __construct($item, $falhas)
    {
        $this->item = $item;
        $this->falhas = $falhas;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚨 Alerta de indisponibilidade - '.$this->item->nome)
            ->greeting('Atenção, Fiscal!')
            ->line("O serviço **{$this->item->nome}** está offline há {$this->falhas} verificações consecutivas.")
            ->line('Endereço/IP: '.$this->item->alvo)
            ->line('Último erro: '.($this->item->erro ?? 'Nenhum'))
            ->line('Verifique com a contratada (PRODEPA ou Montreal) e registre no plano de fiscalização.')
            ->action('Ver no sistema', url('/monitoramentos/'.$this->item->id));
    }
}
