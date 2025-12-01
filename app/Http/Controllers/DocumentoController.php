<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Documento::with('contrato')->orderByDesc('id');

        if ($tipo = trim((string) $request->get('tipo'))) {
            $query->where('tipo', 'like', "%{$tipo}%");
        }
        if ($contrato = trim((string) $request->get('contrato'))) {
            $query->whereHas('contrato', function ($q) use ($contrato) {
                $q->where('numero', 'like', "%{$contrato}%");
            });
        }

        $documentos = $query->paginate(20)->appends($request->query());

        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        $contratos = Contrato::all();

        return view('documentos.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'tipo' => 'nullable|string',
            'documento_tipo_id' => 'nullable|exists:documento_tipos,id',
            'titulo' => 'nullable|string|max:200',
            'descricao' => 'nullable|string|max:500',
            'versao' => 'nullable|string|max:20',
            'caminho_arquivo' => 'nullable|file',
        ]);

        $tiposEnum = ['TR', 'ETP', 'PARECER', 'NOTA_TECNICA', 'RELATORIO', 'OUTROS'];
        $tipoEnum = 'OUTROS';
        if (! empty($validated['tipo']) && in_array($validated['tipo'], $tiposEnum, true)) {
            $tipoEnum = $validated['tipo'];
        }
        if (! empty($validated['documento_tipo_id'])) {
            $tipoEnt = \App\Models\DocumentoTipo::find($validated['documento_tipo_id']);
            if ($tipoEnt) {
                $tipoEnum = in_array($tipoEnt->slug, $tiposEnum, true) ? $tipoEnt->slug : 'OUTROS';
            }
        }

        $savedPath = null;
        if ($request->hasFile('caminho_arquivo')) {
            $file = $request->file('caminho_arquivo');
            $ext = strtolower($file->getClientOriginalExtension() ?? 'pdf');
            $uuid = (string) \Illuminate\Support\Str::uuid();
            $safeOriginal = preg_replace('/[^a-zA-Z0-9_\-\.]+/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $finalName = $safeOriginal ? ("{$safeOriginal}_{$uuid}.{$ext}") : ("documento_{$uuid}.{$ext}");
            $savedPath = $file->storeAs('documentos', $finalName, 'public');
        }

        try {
        $documento = Documento::create([
            'contrato_id' => $validated['contrato_id'],
            'tipo' => $tipoEnum,
            'documento_tipo_id' => $validated['documento_tipo_id'] ?? null,
            'titulo' => $validated['titulo'] ?? null,
            'descricao' => $validated['descricao'] ?? null,
            'caminho_arquivo' => $savedPath,
            'versao' => $validated['versao'] ?? null,
            'data_upload' => now(),
            'created_by' => auth()->id(),
        ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Erro ao cadastrar documento: ' . $e->getMessage());
        }

        // Notificação: documento anexado ao contrato
        notify_event('notificacoes.contratos.documento_anexado', [
            'titulo' => 'Documento anexado',
            'mensagem' => "Documento {$documento->tipo} anexado ao contrato #{$documento->contrato_id}.",
        ], $documento);

        return redirect()->route('documentos.index')->with('success', 'Documento cadastrado!');
    }

    /**
     * Endpoint JSON para DataTables: lista de documentos.
     */
    public function data(Request $request)
    {
        $query = Documento::with('contrato')->orderByDesc('id');

        $docs = $query->get()->map(function ($d) {
            $path = $d->caminho_arquivo;
            $ext = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));
            $icon = 'fa-file';
            $color = '';
            switch ($ext) {
                case 'pdf': $icon = 'fa-file-pdf';
                    $color = 'text-danger';
                    break;
                case 'doc': case 'docx': $icon = 'fa-file-word';
                    $color = 'text-primary';
                    break;
                case 'xls': case 'xlsx': $icon = 'fa-file-excel';
                    $color = 'text-success';
                    break;
                case 'ppt': case 'pptx': $icon = 'fa-file-powerpoint';
                    $color = 'text-danger';
                    break;
                case 'zip': case 'rar': $icon = 'fa-file-archive';
                    $color = 'text-warning';
                    break;
                case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp': $icon = 'fa-file-image';
                    $color = 'text-info';
                    break;
                case 'txt': $icon = 'fa-file-alt';
                    break;
            }

            return [
                'id' => $d->id,
                'tipo' => $d->tipo,
                'titulo' => $d->titulo ?? '-',
                'contrato' => $d->contrato->numero ?? $d->contrato_id,
                'data_upload' => $d->data_upload ?? '-',
                'versao' => $d->versao ?? '-',
                'arquivo' => $path ? [
                    'url' => route('documentos.visualizar', $d->id),
                    'icon' => $icon,
                    'color' => $color,
                ] : null,
            ];
        });

        return response()->json(['data' => $docs]);
    }

    /**
     * Resource "show": redireciona para a página de visualização do PDF.
     */
    public function show(Documento $documento, Request $request)
    {
        $params = [];
        if ($request->filled('return_to')) {
            $params['return_to'] = $request->input('return_to');
        }

        return redirect()->route('documentos.visualizar', array_merge(['documento' => $documento->id], $params));
    }

    /**
     * Exibe uma página com visualizador de PDF para o documento.
     */
    public function visualizar(Documento $documento, Request $request)
    {
        $return_to = $request->input('return_to');
        if (! $return_to) {
            // Fallback para página anterior ou índice de documentos
            $prev = url()->previous();
            $return_to = $prev ?: route('documentos.index');
        }

        return view('documentos.visualizar', compact('documento', 'return_to'));
    }

    /**
     * Transmite o PDF inline para o navegador.
     */
    public function stream(Documento $documento)
    {
        $disk = Storage::disk('public');
        $path = $documento->caminho_arquivo;
        if (! $path || ! $disk->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }
        $stream = $disk->readStream($path);
        return response()->stream(function () use ($stream) {
            if (is_resource($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /**
     * Faz download do PDF do documento.
     */
    public function download(Documento $documento)
    {
        $disk = Storage::disk('public');
        $path = $documento->caminho_arquivo;
        if (! $path || ! $disk->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }
        $full = $disk->path($path);

        return response()->download($full, basename($full), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Página dedicada para imprimir o PDF usando o utilitário de impressão do SO.
     */
    public function print(Documento $documento)
    {
        $disk = Storage::disk('public');
        $path = $documento->caminho_arquivo;
        if (! $path || ! $disk->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // Renderiza uma página simples com iframe que aciona a impressão
        return view('documentos.print', compact('documento'));
    }

    /**
     * Visualiza um arquivo do disco público por caminho (sem registro de Documento).
     */
    public function visualizarPath(Request $request)
    {
        $path = (string) $request->query('path', '');
        $path = ltrim($path, '/\\');
        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }
        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $return_to = $request->input('return_to');
        if (! $return_to) {
            $prev = url()->previous();
            $return_to = $prev ?: route('documentos.index');
        }

        $documento = (object) [
            'titulo' => basename($path),
            'tipo' => 'ARQUIVO',
            'caminho_arquivo' => $path,
            'contrato' => null,
        ];

        $stream_url = route('arquivos.stream', ['path' => $path]);
        $download_url = Storage::url($path);

        return view('documentos.visualizar', compact('documento', 'return_to', 'stream_url', 'download_url'));
    }

    /**
     * Stream de arquivo por caminho (sem registro de Documento).
     */
    public function streamPath(Request $request)
    {
        $path = (string) $request->query('path', '');
        $path = ltrim($path, '/\\');
        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }
        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $contentType = $ext === 'pdf' ? 'application/pdf' : 'application/octet-stream';
        $stream = $disk->readStream($path);
        return response()->stream(function () use ($stream) {
            if (is_resource($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
