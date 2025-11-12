<div class="btn-group" role="group">
  <a href="{{ route('servidores.show', $s) }}" class="btn btn-sm btn-outline-primary">
    <i class="fas fa-eye"></i>
  </a>
  <a href="{{ route('servidores.edit', $s) }}" class="btn btn-sm btn-outline-warning">
    <i class="fas fa-edit"></i>
  </a>
  <form action="{{ route('servidores.destroy', $s) }}" method="POST" onsubmit="return confirm('Remover este servidor?');">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">
      <i class="fas fa-trash"></i>
    </button>
  </form>
</div>
