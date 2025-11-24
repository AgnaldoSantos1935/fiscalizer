<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // 🔹 Se muitas tentativas falhas → bloqueio temporário
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // 🔹 Tentativa de autenticação
        if ($this->attemptLogin($request)) {
            $user = Auth::user();

            // 🛑 Bloqueia usuários com senha expirada
            if ($user->password_expires_at && now()->greaterThan($user->password_expires_at)) {
                Auth::logout();

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans('auth.password_expired')]);
            }

            // 🟢 Permite login normalmente
            return $this->sendLoginResponse($request);
        }

        // 🔁 Incrementa contador de tentativas
        $this->incrementLoginAttempts($request);

        // ❌ Retorno padrão de falha
        return $this->sendFailedLoginResponse($request);
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // 🔒 Se a senha expirou, bloqueia login e guarda flag na sessão
            if ($user->password_expires_at && now()->greaterThan($user->password_expires_at)) {
                session(['login_expired' => true]); // salva antes de deslogar
                Auth::logout();

                return false; // impede o login
            }

            return true;
        }

        return false;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        // 🔹 Verifica se a sessão contém flag de expiração
        if (session('login_expired')) {
            session()->forget('login_expired'); // limpa para não repetir

            return back()
                ->withInput($request->only('email'))
                ->with('expired_message', '🔒 Sua senha expirou. Entre em contato com o administrador do sistema.');
        }

        // 🔹 Caso padrão — erro de autenticação
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Resolve credenciais aceitando e-mails presentes em user_profiles.
     *
     * Caso o e-mail informado exista apenas em `user_profiles.email_institucional`
     * ou `user_profiles.email_pessoal`, mapeia para o e-mail do `users` vinculado.
     */
    protected function credentials(Request $request)
    {
        $emailInput = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        $resolvedEmail = $emailInput;

        // Se não existir usuário com este e-mail, tenta resolver via perfil
        $existsInUsers = User::where('email', $emailInput)->exists();
        if (! $existsInUsers) {
            $profile = null;
            $query = UserProfile::query();
            $hasInst = Schema::hasColumn('user_profiles', 'email_institucional');
            $hasPess = Schema::hasColumn('user_profiles', 'email_pessoal');
            if ($hasInst) {
                $query->orWhere('email_institucional', $emailInput);
            }
            if ($hasPess) {
                $query->orWhere('email_pessoal', $emailInput);
            }
            if ($hasInst || $hasPess) {
                $profile = $query->first();
            }

            if ($profile && $profile->user && $profile->user->email) {
                $resolvedEmail = $profile->user->email;
            }
        }

        return [
            'email' => $resolvedEmail,
            'password' => $password,
        ];
    }
}
