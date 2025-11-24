@extends('layouts.app')

@section('title', 'Validação de Documentos da Medição')

@section('content')
@include('layouts.components.breadcrumbs')
    <div class="container-fluid">

        {{-- Cabeçalho --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="fas fa-file-invoice text-primary me-2"></i>
                Documentos da Medição #{{ $medicao->id }}
            </h4>

            <a href="{{ route('medicoes.show', $medicao->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

        {{-- Mensagens --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        {{-- STATUS GERAL DA VALIDAÇÃO --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                @php
                    $nf = $medicao->notaFiscal ?? null;
                    $status = $nf?->status ?? 'pendente';
                @endphp

                <h5 class="fw-semibold mb-3">
                    <i class="fas fa-check-double me-2 text-primary"></i>Status da Nota Fiscal
                </h5>

                @if (!$nf)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Nenhuma Nota Fiscal enviada ainda.
                    </div>
                @else
                    <div
                        class="p-3 rounded-3
                    @if ($status == 'valido') bg-success text-white
                    @elseif($status == 'invalido') bg-danger text-white
                    @elseif($status == 'erro') bg-warning text-dark
                    @else bg-light @endif">

                        <strong>Status:</strong>
                        @if ($status == 'valido')
                            ✔ Nota Fiscal válida
                        @elseif($status == 'invalido')
                            ✖ Nota Fiscal inválida
                        @elseif($status == 'erro')
                            ⚠ Erro durante validação
                        @else
                            ⏳ Aguardando validação
                        @endif

                        <br>

                        <strong>Mensagem:</strong>
                        {{ $nf->mensagem ?? '—' }}

                        <div class="mt-3">
                            <form action="{{ route('medicoes.documentos.validar_nf', $medicao->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-warning btn-sm">
                                    <i class="fas fa-sync-alt"></i> Revalidar NF
                                </button>
                            </form>

                            @if ($status == 'invalido')
                                <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#substituirNFModal">
                                    <i class="fas fa-upload"></i> Substituir Documento
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- LISTA DE DOCUMENTOS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-folder-open me-2 text-primary"></i>
                    Documentos Enviados
                </h5>
            </div>

            <div class="card-body">

                @if ($medicao->documentos->count() == 0)
                    <div class="alert alert-info">Nenhum documento enviado até o momento.</div>
                @else
                    @if ($nf && $nf->status == 'invalido')
                        <div class="alert alert-danger shadow-sm">
                            <strong>Nota Fiscal Inválida:</strong> {{ $nf->mensagem }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Situação</th>
                                    <th>Mensagem</th>
                                    <th>Arquivo</th>
                                    <th>Validação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medicao->documentos as $doc)
                                    <tr>
                                        <td class="fw-semibold text-capitalize">
                                            {{ str_replace('_', ' ', $doc->tipo) }}
                                        </td>

                                        <td>
                                            @if ($doc->status == 'valido')
                                                <span class="badge bg-success">Válido</span>
                                            @elseif($doc->status == 'invalido')
                                                <span class="badge bg-danger">Inválido</span>
                                            @elseif($doc->status == 'erro')
                                                <span class="badge bg-warning text-dark">Erro</span>
                                            @else
                                                <span class="badge bg-secondary">Pendente</span>
                                            @endif
                                        </td>

                                        <td>{{ $doc->mensagem ?? '—' }}</td>

                                        <td>
                                            @php
                                                $__path = $doc->arquivo ?? '';
                                                $__ext = strtolower(pathinfo($__path, PATHINFO_EXTENSION));
                                                $__icon = 'fa-file'; $__color = '';
                                                switch ($__ext) {
                                                    case 'pdf': $__icon = 'fa-file-pdf'; $__color = 'text-danger'; break;
                                                    case 'doc': case 'docx': $__icon = 'fa-file-word'; $__color = 'text-primary'; break;
                                                    case 'xls': case 'xlsx': $__icon = 'fa-file-excel'; $__color = 'text-success'; break;
                                                    case 'ppt': case 'pptx': $__icon = 'fa-file-powerpoint'; $__color = 'text-danger'; break;
                                                    case 'zip': case 'rar': $__icon = 'fa-file-archive'; $__color = 'text-warning'; break;
                                                    case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp': $__icon = 'fa-file-image'; $__color = 'text-info'; break;
                                                    case 'txt': $__icon = 'fa-file-alt'; break;
                                                }
                                            @endphp
                                            <a href="{{ asset('storage/' . $doc->arquivo) }}"
                                                class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                                                <i class="fas {{ $__icon }} {{ $__color }}"></i> Download
                                            </a>
                                        </td>
                                        <td>
                                            @if ($doc->status == 'invalido')
                                                <span class="text-danger fw-bold">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    {{ $doc->mensagem }}
                                                </span>
                                            @elseif($doc->status == 'erro')
                                                <span class="text-warning fw-bold">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    {{ $doc->mensagem }}
                                                </span>
                                            @else
                                                {{ $doc->mensagem ?? '—' }}
                                            @endif
                                        </td>


                                        <td>
                                            <form
                                                action="{{ route('medicoes.documentos.revalidar', [$medicao->id, $doc->id]) }}"
                                                method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-sync"></i> Revalidar
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- LINHA DO TEMPO --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <h5 class="fw-semibold mb-0">
                    <i class="fas fa-stream me-2 text-primary"></i>
                    Linha do Tempo da Validação
                </h5>
            </div>

            <div class="card-body">

                @if ($medicao->logs->count() == 0)
                    <p class="text-muted">Nenhum evento registrado ainda.</p>
                @else
                    <ul class="timeline">
                        @foreach ($medicao->logs as $log)
                            <li class="timeline-item mb-5">
                                <h6 class="fw-bold">
                                    {{ $log->acao }}
                                </h6>
                                <small class="text-muted d-block">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </small>
                                <p class="mt-2">{{ $log->mensagem }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>

    </div>

    {{-- MODAL de Substituição da NF --}}
    <div class="modal fade" id="substituirNFModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice me-2"></i> Substituir Nota Fiscal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('medicoes.documentos.substituir_nf', $medicao->id) }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">

                        <p class="text-muted">
                            A nota fiscal enviada é inválida. Envie uma nova NF (PDF ou XML).
                        </p>

                        <input type="file" class="form-control" name="nova_nf" required>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger">
                            <i class="fas fa-upload me-2"></i> Enviar nova NF
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection
