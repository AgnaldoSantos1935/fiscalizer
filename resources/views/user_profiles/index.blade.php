@extends('layouts.app')

@section('title', 'Perfis de Usuário')

@section('content_header')

    <h1><i class="fas fa-users me-2"></i>Perfis de Usuários</h1>
@stop

@section('content')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
        <table id="tabelaPerfis" class="table table-striped table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th></th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Cargo</th>
                    <th>DRE</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    $('#tabelaPerfis').DataTable({
        processing: false,
        serverSide: true,
        ajax: "{{ route('user_profiles.index') }}",
        columns: [
            { data: 'id', name: 'id', visible:false },
            { data: 'nome_completo', name: 'nome_completo' },
            { data: 'cpf', name: 'cpf' },
            { data: 'cargo', name: 'cargo' },
            { data: 'dre', name: 'dre' },
            { data: 'celular', name: 'celular', defaultContent: '' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
        ],
        language: {
            url: 'js/pt-BR.json'
        },
        responsive: true,
        order: [[1, 'asc']]
    });
});
</script>
@stop
