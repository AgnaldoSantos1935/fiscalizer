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

        Documento::create($validated);

        return redirect()->route('documentos.index')->with('success', 'Documento cadastrado!');
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
