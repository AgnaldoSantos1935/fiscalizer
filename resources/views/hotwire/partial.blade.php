@php($n = (int)($count ?? 0))
<turbo-frame id="demo">
  <div class="d-flex align-items-center gap-3">
    <div class="display-6">{{ $n }}</div>
    <a href="{{ route('hotwire.partial', ['count' => $n + 1]) }}" class="btn btn-primary">Incrementar</a>
    <a href="{{ route('hotwire.test') }}" class="btn btn-outline-secondary">Reset</a>
  </div>
</turbo-frame>
