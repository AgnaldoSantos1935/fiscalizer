<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Events\NewNotification;
use App\Notifications\UserPushNotification;

class NotificationService
{
    public static function enviar(User $user, string $titulo, ?string $mensagem = null, ?string $tipo = null, ?string $link = null)
    {
        $noti = UserNotification::create([
            'user_id'  => $user->id,
            'titulo'   => $titulo,
            'mensagem' => $mensagem,
            'tipo'     => $tipo,
            'link'     => $link,
        ]);

        broadcast(new NewNotification($noti))->toOthers();

        try {
            $user->notify(new UserPushNotification($titulo, $mensagem, $link));
        } catch (\Throwable $e) {}

        return $noti;
    }
}
