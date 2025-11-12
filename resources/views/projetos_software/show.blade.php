<ul class="nav nav-tabs mb-4" id="tabsProjeto" role="tablist">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#requisitos">Requisitos</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#atividades">Atividades</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#cronograma">Cronograma</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#equipe">Equipe</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade show active" id="requisitos">@include('projetos.partials.requisitos')</div>
  <div class="tab-pane fade" id="atividades">@include('projetos.partials.atividades')</div>
  <div class="tab-pane fade" id="cronograma">@include('projetos.partials.cronograma')</div>
  <div class="tab-pane fade" id="equipe">@include('projetos.partials.equipe')</div>
</div>
