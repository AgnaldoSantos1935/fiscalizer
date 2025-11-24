<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    protected function throttleKey(Request $request)
    {
        // ⚙️ Aqui definimos um throttle diferente (ex: 10 segundos)
        return strtolower($request->input($this->username())) . '|' . $request->ip();
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        // Mensagem amigável em português e redireciona para a tela de login
        // Usa caminho relativo para preservar host/porta da requisição atual
        return redirect()->to('/login')
            ->with('status', 'Enviamos um link de recuperação de senha para o e-mail cadastrado. Verifique sua caixa de entrada e também a pasta de spam.');
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        // Mantém comportamento padrão: volta com erro traduzido
        return back()->withErrors([
            'email' => trans($response),
        ]);
    }
}
