{{--
    Tema Fiscalizer: referência rápida de personalização
    - Variáveis (cores, bg, links): em `resources/css/custom.css` no `:root`.
    - Classes AdminLTE (layout do painel): defina em `config/adminlte.php`
        • `classes_sidebar`  com `sidebar-custom`
        • `classes_topnav`   com `topnav-custom`
        • `classes_content_wrapper` com `content-custom`
        • `classes_body`     com `footer-custom theme-fiscalizer`
    - Limpe cache se necessário: `php artisan optimize:clear`.
--}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid auth-login">
    <div class="row g-0 min-vh-100 align-items-center">
        <div class="col-12 col-md-6 col-lg-5 col-xl-4 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg w-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="login-title">Redefinir Senha</div>
                        <div class="title-divider"></div>
                    </div>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input id="email" type="email" class="ui-input @error('email') is-invalid @enderror h-45px" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Digite seu e-mail" readonly>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input id="password" type="password" class="ui-input @error('password') is-invalid @enderror h-45px" name="password" required autocomplete="new-password" placeholder="Digite sua nova senha">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">Confirmar Senha</label>
                            <input id="password-confirm" type="password" class="ui-input h-45px" name="password_confirmation" required autocomplete="new-password" placeholder="Confirme sua nova senha">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="ui-btn">
                                Redefinir Senha
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">Quer voltar?
                        <a href="{{ route('login') }}" class="ui-cta ms-1">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection