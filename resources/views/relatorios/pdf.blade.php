<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Usuário</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        @foreach($relatorios as $r)
        <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->titulo }}</td>
            <td>{{ $r->tipo }}</td>
            <td>{{ $r->user->name ?? '-' }}</td>
            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
