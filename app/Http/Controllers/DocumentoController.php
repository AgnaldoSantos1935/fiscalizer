<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('contrato')->get();

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
            'tipo' => 'required|string',
            'titulo' => 'nullable|string|max:200',
            'caminho_arquivo' => 'nullable|file',
        ]);

        $documento = Documento::create($validated);

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
                    'url' => \Illuminate\Support\Facades\Storage::url($path),
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
        $full = $disk->path($path);

        return response()->file($full, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($full) . '"',
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
}
