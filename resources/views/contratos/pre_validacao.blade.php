@extends('layouts.app')

@section('content')
    <div class="container">

        <h3 class="mb-4">Revisão de Informações Extraídas</h3>

        <form action="{{ route('contratos.salvar') }}" method="POST">
            @csrf

            <input type="hidden" name="arquivo_path" value="{{ $arquivo_path }}">

            <div class="card border-0 shadow-sm mb-4 p-3">

                <h5 class="mb-3">Dados do Contrato</h5>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5>Risco Contratual</h5>
                        <p>
                            Score: <strong>{{ $risco['score'] }}</strong><br>
                            Nível: <strong>{{ $risco['nivel'] }}</strong>
                        </p>
                        @if (count($risco['detalhes']))
                            <ul class="small">
                                @foreach ($risco['detalhes'] as $d)
                                    <li>{{ $d['descricao'] }} (−{{ $d['peso'] }} pontos)</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label>Número do Contrato</label>
                        <input type="text" name="numero" class="form-control" value="{{ $dados['numero'] }}">
                    </div>

                    <div class="col-md-4">
                        <label>Processo</label>
                        <input type="text" name="processo_origem" class="form-control"
                            value="{{ $dados['processo_origem'] }}">
                    </div>

                    <div class="col-md-4">
                        <label>Modalidade</label>
                        <input type="text" name="modalidade" class="form-control" value="{{ $dados['modalidade'] }}">
                    </div>
                </div>

                <div class="mt-4">
                    <label>Objeto Completo</label>
                    <textarea name="objeto" class="form-control" rows="4">{{ $dados['objeto'] }}</textarea>
                </div>

                <div class="mt-4">
                    <label>Objeto Resumido</label>
                    <textarea name="objeto_resumido" class="form-control" rows="2">{{ $dados['objeto_resumido'] }}</textarea>
                </div>

            </div>

            <div class="card border-0 shadow-sm mb-4 p-3">
                <h5 class="mb-3">Empresa</h5>

                <div class="row">
                    <div class="col-md-6">
                        <label>Razão Social</label>
                        <input type="text" name="empresa_razao_social" class="form-control"
                            value="{{ $dados['empresa']['razao_social'] }}">
                    </div>

                    <div class="col-md-3">
                        <label>CNPJ</label>
                        <input type="text" name="empresa_cnpj" class="form-control"
                            value="{{ $dados['empresa']['cnpj'] }}">
                    </div>

                    <div class="col-md-3">
                        <label>Representante</label>
                        <input type="text" name="empresa_representante" class="form-control"
                            value="{{ $dados['empresa']['representante'] }}">
                    </div>
                </div>
            </div>

            <button class="btn btn-primary btn-lg w-100">
                Salvar Contrato no Sistema
            </button>

        </form>

    </div>
@endsection
