<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notificacoes = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        $naoLidas = UserNotification::where('user_id', Auth::id())
            ->where('lida', false)
            ->count();

        return view('notificacoes.index', compact('notificacoes', 'naoLidas'));
    }

    /**
     * Endpoint JSON para DataTables: lista de notificações do usuário autenticado.
     */
    public function data()
    {
        $userId = Auth::id();
        abort_unless($userId, 401);

        $items = UserNotification::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'lida' => (bool) $n->lida,
                    'titulo' => $n->titulo,
                    'mensagem' => $n->mensagem,
                    'link' => $n->link,
                    'recebida' => $n->created_at?->format('d/m/Y H:i'),
                ];
            });

        return response()->json(['data' => $items]);
    }

    public function marcarLida(UserNotification $notificacao)
    {
        abort_if($notificacao->user_id !== Auth::id(), 403);

        $notificacao->update([
            'lida' => true,
            'lida_em' => now(),
        ]);

        return back();
    }

    public function marcarTodas()
    {
        UserNotification::where('user_id', Auth::id())
            ->where('lida', false)
            ->update([
                'lida' => true,
                'lida_em' => now(),
            ]);

        return back();
    }

    public function teste()
    {
        $user = Auth::user();
        abort_unless($user, 401);

        NotificationService::enviar(
            $user,
            'Notificação de teste',
            'Este é um teste do sino de notificações.',
            'teste',
            route('home')
        );

        return back()->with('success', 'Notificação de teste enviada. Abra o sino para visualizá-la.');
    }
}
