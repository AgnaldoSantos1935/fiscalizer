@extends('layouts.publico')

@section('title','Galeria de Imagens — Fiscalizer')

@section('content')
<header class="gov-header py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="brand">
      <img src="{{ asset('img/logo/fiscalizer-sistema.svg') }}" alt="Fiscalizer" height="32">
      <div class="d-flex flex-column">
        <strong>Governo do Estado de Exemplo</strong>
        <span class="small">SEDUC-EX • Fiscalizer</span>
      </div>
    </div>
    <nav class="d-flex align-items-center gap-3">
      <a href="{{ url('/') }}" class="text-white text-decoration-none">Início</a>
      <a href="#galeria" class="text-white text-decoration-none">Galeria</a>
      <a href="{{ route('site.contato.enviar') }}" class="btn btn-acesso btn-sm ms-2">Contato</a>
    </nav>
  </div>
  </header>

<section id="galeria" class="py-5 bg-white">
  <div class="container">
    <h3 class="section-title mb-3">Galeria de Imagens</h3>
    @php
      $root = public_path('img');
      $all = collect(\Illuminate\Support\Facades\File::allFiles($root))
        ->filter(fn($f) => preg_match('/\.(png|svg|jpg|jpeg|webp)$/i', $f->getFilename()))
        ->map(function($f){
          $rel = str_replace(public_path(), '', $f->getPathname());
          $rel = str_replace('\\', '/', $rel);
          return ltrim($rel, '/');
        });
      $groups = $all->groupBy(function($p){
        $parts = explode('/', $p);
        $i = array_search('img', $parts);
        $dir = $i !== false && isset($parts[$i+1]) ? $parts[$i+1] : 'img';
        return $dir;
      })->sortKeys();
    @endphp
    @foreach($groups as $dir => $files)
      <h5 class="mt-4 mb-2">{{ ucfirst($dir) }}</h5>
      <div class="row g-3">
        @foreach($files as $p)
          <div class="col-6 col-md-3">
            <div class="card module-card h-100">
              <div class="card-body">
                <img src="{{ asset($p) }}" alt="{{ basename($p) }}" class="card-media" onerror="this.style.display='none'" />
                <div class="small text-muted mt-1">{{ $p }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endforeach
  </div>
</section>
@endsection
