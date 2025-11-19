@extends('layouts.auth')

@section('content')
@include('layouts.components.breadcrumbs')
    <div class="container auth-login">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card shadow-lg mt-5">
        <div class="card-header bg-primary text-white">
            <h3 class="text-center font-weight-light my-2">{{ config('adminlte.title', config('app.name', 'Fiscalizer')) }}</h3>
        </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="brand-title">Dados de Usuário</div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger text-center shadow-sm">
                                <i class="fas fa-lock me-1"></i> {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text h-45px"><i
                                                class="fas fa-envelope fa-lg"></i></span>
                                    </div>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus
                                        placeholder="Digite seu e-mail" class="h-45px">

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
                                        <span class="input-group-text h-45px"><i
                                                class="fas fa-lock fa-lg"></i></span>
                                    </div>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password" placeholder="Digite sua senha"
                                        class="h-45px">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
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
                        <div class="small font-weight-bold">Sistema de Fiscalização de Obras e Serviços</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection