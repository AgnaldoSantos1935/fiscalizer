<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class UserPushNotification extends Notification
{
    public function __construct(
        public string $titulo,
        public ?string $mensagem = null,
        public ?string $link = null
    ) {}

    public function via($notifiable) {
        return ['webpush'];
    }

    public function toWebPush($notifiable, $notification = null) {
        return (new WebPushMessage)
            ->title($this->titulo)
            ->body($this->mensagem)
            ->action('Abrir', $this->link ?? '/');
    }
}
