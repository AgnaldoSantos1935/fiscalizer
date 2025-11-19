<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\DocumentoTecnico;
use App\Services\DocumentoTecnicoIaService;
use App\Services\OrdemServicoPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FluxoOrdemServicoController extends Controller
{
    public function __construct(
        protected DocumentoTecnicoIaService $docIa,
        protected OrdemServicoPdfService $osPdf
    ) {}

    /**
     * 1. DETEC registra a demanda (tela interna)
     */
    public function criarDemanda()
    {
        return view('demandas.fluxo.create');
    }

    public function salvarDemanda(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'sistema_id' => 'nullable|integer',
            'modulo_id' => 'nullable|integer',
            'tipo_manutencao' => 'required|string',
            'prioridade' => 'required|string',
            'email_empresa' => 'required|email',
        ]);

        $demanda = Demanda::create([
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'] ?? null,
            'sistema_id' => $data['sistema_id'] ?? null,
            'modulo_id' => $data['modulo_id'] ?? null,
            'tipo_manutencao' => $data['tipo_manutencao'],
            'prioridade' => $data['prioridade'],
            'status' => 'Aguardando Documento da Empresa',
            'data_abertura' => now(),
        ]);

        // gera token único pra upload público
        $token = Str::uuid()->toString();
        $demanda->upload_token = $token;
        $demanda->save();

        // dispara e-mail pra empresa
        $this->enviarEmailParaEmpresa($demanda, $data['email_empresa']);

        return redirect()
            ->route('demandas.show', $demanda)
            ->with('success', 'Demanda registrada e e-mail enviado à empresa.');
    }

    protected function enviarEmailParaEmpresa(Demanda $demanda, string $email)
    {
        $urlUpload = route('empresa.upload_documento', $demanda->upload_token);

        Mail::raw(
            "Prezados,\n\nUma nova demanda foi registrada.\n" .
            "Número da demanda: {$demanda->id}\n" .
            "Título: {$demanda->titulo}\n\n" .
            'Por favor, acessem o link a seguir para enviar o documento técnico ' .
            "de levantamento de requisitos, cronograma, protótipos e estimativa de PF/UST:\n" .
            "{$urlUpload}\n\nAtenciosamente,\nDETEC / SEDUC-PA",
            function ($m) use ($email) {
                $m->to($email)->subject('Nova Demanda - Envio de Documento Técnico');
            }
        );
    }

    /**
     * 2. Página pública para empresa enviar o documento técnico
     */
    public function formUploadEmpresa(string $token)
    {
        $demanda = Demanda::where('upload_token', $token)->firstOrFail();

        return view('empresa.upload_documento', compact('demanda', 'token'));
    }

    public function receberDocumentoEmpresa(Request $request, string $token)
    {
        $demanda = Demanda::where('upload_token', $token)->firstOrFail();

        $request->validate([
            'documento' => 'required|file|max:51200', // 50MB
        ]);

        $file = $request->file('documento');
        $path = $file->store("demandas/{$demanda->id}/documento_tecnico", 'public');

        $doc = DocumentoTecnico::create([
            'demanda_id' => $demanda->id,
            'arquivo_path' => $path,
            'arquivo_original' => $file->getClientOriginalName(),
            'status_validacao' => 'pendente',
        ]);

        // Atualiza status
        $demanda->status = 'Documento Técnico Recebido';
        $demanda->save();

        // dispara processamento automático com IA
        $this->docIa->processar($doc);

        return view('empresa.upload_confirmado', compact('demanda'));
    }

    /**
     * 3. Tela interna para ver resultado da análise e emitir OS se válido
     */
    public function analisarDocumento(Demanda $demanda)
    {
        $doc = $demanda->documentosTecnicos()->latest()->first();

        if (! $doc) {
            return back()->with('error', 'Nenhum documento técnico recebido para esta demanda.');
        }

        return view('demandas.fluxo.analisar_documento', compact('demanda', 'doc'));
    }

    /**
     * 4. Emissão automática de Ordem de Serviço em PDF
     */
    public function emitirOs(Demanda $demanda)
    {
        $doc = $demanda->documentosTecnicos()->latest()->firstOrFail();

        if ($doc->status_validacao !== 'valido') {
            return back()->with('error', 'Documento técnico ainda não está validado para emissão de OS.');
        }

        // gera OS (número, grava em bd e cria PDF)
        $os = $this->osPdf->gerarParaDemanda($demanda, $doc);

        // Atualiza demanda
        $demanda->status = 'OS Emitida';
        $demanda->save();

        return redirect()
            ->route('ordens_servico.show', $os->id)
            ->with('success', 'Ordem de Serviço emitida automaticamente.');
    }
}
