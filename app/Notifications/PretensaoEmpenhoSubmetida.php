<?php

namespace App\Notifications;

use App\Models\Empenho;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PretensaoEmpenhoSubmetida extends Notification
{
    use Queueable;

    public function __construct(public Empenho $empenho, public int $mes, public int $ano) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('empenhos/' . $this->empenho->id);

        return (new MailMessage)
            ->subject('Pretensão de Empenho Submetida — ' . $this->empenho->numero)
            ->greeting('Olá, Gestor(a)')
            ->line('A pretensão de empenho foi submetida para avaliação.')
            ->line('Contrato: ' . $this->empenho->contrato->numero)
            ->line('Empresa: ' . $this->empenho->empresa->razao_social)
            ->line('Mês/Ano: ' . $this->mes . '/' . $this->ano)
            ->action('Abrir Empenho', $url)
            ->line('Sistema Fiscalizer — SEDUC/PA');
    }
}
