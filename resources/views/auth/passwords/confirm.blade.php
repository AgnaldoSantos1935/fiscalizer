@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="card shadow-lg border-0 rounded-lg mt-3">
                <div class="card-header bg-white text-center py-3">
                    <h3 class="text-center">Confirmar Senha</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3 text-muted">Por favor, confirme sua senha antes de continuar.</p>
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input id="password" type="password" class="ui-input @error('password') is-invalid @enderror h-45px" name="password" required autocomplete="current-password" placeholder="Digite sua senha">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="ui-btn">Confirmar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection