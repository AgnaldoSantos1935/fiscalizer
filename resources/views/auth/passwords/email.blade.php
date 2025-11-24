{{--
    Tema Fiscalizer: onde alterar cores e estilos
    - Variáveis do tema: `resources/css/custom.css` (variáveis em `:root`).
    - Classes de tema AdminLTE (aplicadas no painel): `config/adminlte.php`
        • `classes_sidebar`  → `sidebar-custom`
        • `classes_topnav`   → `topnav-custom`
        • `classes_content_wrapper` → `content-custom`
        • `classes_body`     → `footer-custom theme-fiscalizer`
    - Caso não veja as mudanças, use: `php artisan optimize:clear`.
--}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid auth-login">
    <div class="row g-0 min-vh-100 align-items-center">
        <div class="col-12 col-md-6 col-lg-5 col-xl-4 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg w-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="login-title">Recuperar Senha</div>
                        <div class="title-divider"></div>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input id="email" type="email" class="ui-input @error('email') is-invalid @enderror h-45px" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Digite seu e-mail">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="ui-btn">
                                Enviar Link de Recuperação
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