{{--
    Tema Fiscalizer: cores e estilos
    - Variáveis de tema: edite em `resources/css/custom.css` (bloco `:root`).
    - Classes AdminLTE: configure em `config/adminlte.php`
        • `classes_sidebar` (ex.: `sidebar-custom`)
        • `classes_topnav`  (ex.: `topnav-custom`)
        • `classes_content_wrapper` (ex.: `content-custom`)
        • `classes_body` (ex.: `footer-custom theme-fiscalizer`)
    - Após alterações, limpe cache se necessário: `php artisan optimize:clear`.
--}}
@extends('layouts.app')

@section('content')
<div class="container auth-login">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-center font-weight-light my-2">{{ config('adminlte.title', config('app.name', 'Fiscalizer')) }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
            <img src="{{ asset('img/logo/fiscalizer-sistema.svg') }}" alt="Fiscalizer Sistema" style="max-height: 100px;">
                    </div>
                    <div class="text-center mb-3">
                        <div class="brand-title">Dados de Usuário</div>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Digite seu e-mail">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Digite sua senha">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Lembrar-me
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            @if (Route::has('password.request'))
                                <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                    Esqueceu sua senha?
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt mr-1"></i> Entrar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">Sistema de Fiscalização de Contratos</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS inline removido; regras vêm de resources/css/custom.css (escopo .auth-login) --}}
@endsection
