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
@include('layouts.components.breadcrumbs')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg mt-3">
                <div class="card-header bg-white text-center py-3">
                    <img src="{{ asset('img/logo/fiscalizer-sistema.png') }}" alt="Fiscalizer Sistema" height="60" class="mb-3">
                    <h3 class="text-center">Redefinir Senha</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text h-45px">
                                        <i class="fas fa-envelope fa-lg"></i>
                                    </span>
                                </div>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror h-45px" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Digite seu e-mail" readonly>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text h-45px">
                                        <i class="fas fa-lock fa-lg"></i>
                                    </span>
                                </div>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror h-45px" name="password" required autocomplete="new-password" placeholder="Digite sua nova senha">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">Confirmar Senha</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text h-45px">
                                        <i class="fas fa-lock fa-lg"></i>
                                    </span>
                                </div>
                                <input id="password-confirm" type="password" class="form-control h-45px" name="password_confirmation" required autocomplete="new-password" placeholder="Confirme sua nova senha">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Redefinir Senha
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <a href="{{ route('login') }}" class="text-decoration-none">Voltar para o login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection