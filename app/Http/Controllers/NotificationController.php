<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index() {
        $notificacoes = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('notificacoes.index', compact('notificacoes'));
    }

    public function marcarLida(UserNotification $notificacao) {
        abort_if($notificacao->user_id !== Auth::id(), 403);

        $notificacao->update([
            'lida' => true,
            'lida_em' => now()
        ]);

        return back();
    }

    public function marcarTodas() {
        UserNotification::where('user_id', Auth::id())
            ->where('lida', false)
            ->update([
                'lida' => true,
                'lida_em' => now()
            ]);

        return back();
    }
}
