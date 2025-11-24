{{--
    Tema Fiscalizer: como ajustar cores e tema
    - Variáveis de cor e fundo: `resources/css/custom.css` (seção `:root`).
    - Classes de tema AdminLTE (para o painel): em `config/adminlte.php`
        • `classes_sidebar` → use `sidebar-custom`
        • `classes_topnav`  → use `topnav-custom`
        • `classes_content_wrapper` → `content-custom`
        • `classes_body`    → `footer-custom theme-fiscalizer`
    - Dica: crie presets no `custom.css` e aplique no `<body>`.
--}}
@extends('layouts.auth')

@section('content')
<div class="container-fluid auth-login">
    <div class="row g-0 min-vh-100 align-items-center">
        <div class="col-12 col-md-6 col-lg-5 col-xl-4 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg w-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="login-title">Registrar</div>
                        <div class="title-divider"></div>
                    </div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input id="name" type="text" class="ui-input @error('name') is-invalid @enderror h-45px" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Digite seu nome">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input id="email" type="email" class="ui-input @error('email') is-invalid @enderror h-45px" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Digite seu e-mail">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input id="password" type="password" class="ui-input @error('password') is-invalid @enderror h-45px" name="password" required autocomplete="new-password" placeholder="Digite sua senha">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">Confirmar Senha</label>
                            <input id="password-confirm" type="password" class="ui-input h-45px" name="password_confirmation" required autocomplete="new-password" placeholder="Confirme sua senha">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="ui-btn">
                                Registrar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">Já tem uma conta?
                        <a href="{{ route('login') }}" class="ui-cta ms-1">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection