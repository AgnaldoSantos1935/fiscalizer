<h3>Relatório NOC – Disponibilidade</h3>

<table width="100%" border="1" cellpadding="4" cellspacing="0">
    <tr>
        <th>Host</th>
        <th>Alvo</th>
        <th>Status</th>
    </tr>

@foreach($hosts as $h)
    <tr>
        <td>{{ $h->nome_conexao }}</td>
        <td>{{ $h->host_alvo }}</td>
        <td>{{ $h->status ? 'ONLINE' : 'OFFLINE' }}</td>
    </tr>
@endforeach
</table>
