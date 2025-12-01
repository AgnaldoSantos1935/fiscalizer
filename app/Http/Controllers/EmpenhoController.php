<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Documento;
use App\Models\Empenho;
use App\Models\EmpenhoSolicitacao;
use App\Models\Empresa;
use App\Models\LogSistema;
use App\Models\SolicitacaoEmpenho;
use App\Notifications\PretensaoEmpenhoSubmetida;
use App\Services\FinanceiroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EmpenhoController extends Controller
{
    /** 🔹 DataTables AJAX */
    public function getData()
    {
        $query = Empenho::with(['empresa', 'contrato'])->select('empenhos.*');

        return DataTables::of($query)
            ->addColumn('empresa', fn ($e) => $e->empresa->razao_social ?? '—')
            ->addColumn('contrato', fn ($e) => $e->contrato->numero ?? '—')
            ->editColumn('valor_total', fn ($e) => $e->valor_total_formatado)
            ->editColumn('data_lancamento', fn ($e) => $e->data_formatada)
            ->addColumn('acoes', function ($e) {
                return '
                    <a href="' . route('empenhos.show', $e->id) . '" class="btn btn-sm btn-outline-primary me-1" title="Visualizar">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="' . route('empenhos.destroy', $e->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm(\'Excluir empenho?\')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>';
            })
            ->rawColumns(['acoes'])
            ->make(true);
    }

    /** 🔹 Lista inicial */
    public function index()
    {
        return view('empenhos.index');
    }

    /** 🔹 Exibição detalhada */
    public function show($id)
    {
        $nota = Empenho::with(['itens', 'empresa', 'contrato'])->findOrFail($id);

        return view('empenhos.show', compact('nota'));
    }

    /** 🔹 Formulário de cadastro */
    public function create()
    {
        $empresas = Empresa::orderBy('razao_social')->get();
        $contratos = Contrato::orderBy('numero')->get();
        $preContratoId = request('contrato_id');
        $preEmpresaId = null;
        $preContrato = null;
        if ($preContratoId) {
            $preContrato = Contrato::find($preContratoId);
            if ($preContrato) {
                $preEmpresaId = $preContrato->contratada_id;
            }
        }

        return view('empenhos.create', compact('empresas', 'contratos'))
            ->with('preContratoId', $preContratoId)
            ->with('preEmpresaId', $preEmpresaId)
            ->with('preContrato', $preContrato);
    }

    /** 🔹 Gravação */
    public function store(Request $request)
    {
        \Log::info('EmpenhoController@store invoked from test', [
            'route' => 'empenhos.store',
            'payload_keys' => array_keys($request->all()),
        ]);
        $validated = $request->validate([
            'numero' => 'required|string|max:30|unique:empenhos,numero',
            'empresa_id' => 'required|exists:empresas,id',
            'contrato_id' => 'required|exists:contratos,id',
            'data_lancamento' => 'nullable|date',
            'processo' => 'nullable|string|max:50',
            'programa_trabalho' => 'nullable|string|max:50',
            'fonte_recurso' => 'nullable|string|max:50',
            'natureza_despesa' => 'nullable|string|max:30',
            'valor_extenso' => 'nullable|string|max:255',
            'itens.*.descricao' => 'required|string|max:255',
            'itens.*.quantidade' => 'required',
            'itens.*.valor_unitario' => 'required',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $empenho = Empenho::create($validated);

            foreach ($request->itens ?? [] as $item) {
                $qtd = $this->brToDecimal($item['quantidade']);
                $vu = $this->brToDecimal($item['valor_unitario']);
                $empenho->itens()->create([
                    'descricao' => $item['descricao'],
                    'quantidade' => $qtd,
                    'valor_unitario' => $vu,
                    'valor_total' => ($qtd ?? 0) * ($vu ?? 0),
                ]);
            }

            $empenho->update(['valor_total' => $empenho->itens()->sum('valor_total')]);
        });

        return redirect()->route('contratos.show', $validated['contrato_id'])
            ->with('success', 'Nota de Empenho cadastrada com sucesso!');
    }

    /** 🔹 Excluir */
    public function destroy($id)
    {
        Empenho::findOrFail($id)->delete();

        return back()->with('success', 'Nota de Empenho removida.');
    }

    /** 🔹 PDF */
    public function imprimir($id)
    {
        $nota = Empenho::with(['itens', 'empresa', 'contrato'])->findOrFail($id);
        $pdf = Pdf::loadView('empenhos.pdf', compact('nota'))->setPaper('a4', 'portrait');

        return Pdf::loadView('empenhos.pdf', compact('nota'))
            ->setPaper('a4', 'portrait')
            ->stream('Empenho_' . $nota->numero . '.pdf');

    }

    /**
     * PDF de Pretensão de Empenho para submissão ao gestor.
     * Aceita ?mes=1..12 e ?ano=YYYY via query string.
     */
    public function pretensaoPdf($id)
    {
        $empenho = Empenho::with(['empresa', 'contrato'])->findOrFail($id);

        $mes = (int) request()->query('mes', 0);
        $ano = (int) request()->query('ano', (int) date('Y'));

        if ($mes < 1 || $mes > 12) {
            $mes = $empenho->data_lancamento ? (int) date('n', strtotime($empenho->data_lancamento)) : (int) date('n');
        }

        $mesNome = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ][$mes] ?? '';

        $pdf = Pdf::loadView('empenhos.pretensao_pdf', [
            'empenho' => $empenho,
            'mes' => $mes,
            'mesNome' => $mesNome,
            'ano' => $ano,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Pretensao_Empenho_' . $empenho->numero . '_' . $mes . '-' . $ano . '.pdf');
    }

    /**
     * Recebe upload do PDF emitido e marca etapa como concluída.
     */
    public function uploadEmitidoPdf(Request $request, $id)
    {
        $empenho = Empenho::findOrFail($id);
        $validated = $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240', // até 10MB
        ]);

        $file = $validated['pdf'];
        $nome = 'empenho_' . $empenho->id . '_emitido_' . date('Ymd_His') . '.pdf';
        $path = $file->storeAs('empenhos_emitidos', $nome, 'public');

        $empenho->emitido_pdf_path = $path;
        $empenho->emitido_at = now();
        $empenho->save();

        return response()->json([
            'success' => true,
            'message' => 'PDF emitido enviado com sucesso.',
            'path' => $path,
            'emitido_at' => $empenho->emitido_at->toIso8601String(),
        ]);
    }

    /**
     * Recebe upload do comprovante de liquidação e marca etapa Pago como concluída.
     */
    public function uploadComprovanteLiquidacao(Request $request, $id)
    {
        $empenho = Empenho::findOrFail($id);
        $validated = $request->validate([
            'comprovante' => 'required|file|mimes:pdf|max:10240', // até 10MB
        ]);

        $file = $validated['comprovante'];
        $nome = 'empenho_' . $empenho->id . '_liquidacao_' . date('Ymd_His') . '.pdf';
        $path = $file->storeAs('empenhos_liquidacoes', $nome, 'public');

        $empenho->pago_comprovante_path = $path;
        $empenho->pago_at = now();
        $empenho->save();

        return response()->json([
            'success' => true,
            'message' => 'Comprovante de liquidação enviado com sucesso.',
            'path' => $path,
            'pago_at' => optional($empenho->pago_at)->toIso8601String(),
        ]);
    }

    /**
     * Formulário: registrar empenho a partir de uma solicitação (solicitacoes_empenho).
     */
    public function registrarFromSolicitacaoForm($solicitacaoId)
    {
        $sol = SolicitacaoEmpenho::with(['contrato', 'medicao', 'solicitante'])->findOrFail($solicitacaoId);
        $empresas = Empresa::orderBy('razao_social')->get();

        return view('empenhos.registrar', compact('sol', 'empresas'));
    }

    /**
     * Persistência do registro de empenho a partir da solicitação.
     */
    public function registrarFromSolicitacaoStore(\App\Http\Controllers\Requests\RegistrarEmpenhoRequest $request, $solicitacaoId, FinanceiroService $financeiro)
    {
        $sol = SolicitacaoEmpenho::findOrFail($solicitacaoId);

        // Upload opcional do PDF oficial
        $dados = $request->validated();
        if ($request->hasFile('pdf_oficial')) {
            $path = $request->file('pdf_oficial')->store('empenhos_oficiais', 'public');
            $dados['pdf_oficial'] = $path;
        }

        $empenhoId = $financeiro->registrarEmpenho($solicitacaoId, $dados, $request->user()->id);

        return redirect()->route('empenhos.show', $empenhoId)
            ->with('success', 'Empenho registrado com sucesso.');
    }

    /**
     * Marca a pretensão como solicitada, registra log e notifica o gestor.
     */
    public function solicitarPretensao(Request $request, $id)
    {
        $empenho = Empenho::with(['contrato.gestor.user', 'empresa'])->findOrFail($id);
        $validated = $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'ano' => 'required|integer|min:2000|max:2100',
            'periodo_referencia' => 'nullable|string|max:120',
            'observacoes' => 'nullable|string|max:500',
            'dados' => 'nullable', // JSON livre de campos adicionais
        ]);

        // Cria registro de solicitação (pendente)
        $sol = EmpenhoSolicitacao::create([
            'empenho_id' => $empenho->id,
            'contrato_id' => $empenho->contrato_id,
            'mes' => (int) $validated['mes'],
            'ano' => (int) $validated['ano'],
            'periodo_referencia' => $validated['periodo_referencia'] ?? null,
            'observacoes' => $validated['observacoes'] ?? null,
            'dados' => $validated['dados'] ?? null,
            'status' => 'pendente',
            'solicitado_by' => Auth::id(),
            'solicitado_at' => now(),
        ]);

        // Marca o empenho como solicitado (para fins de badge/UI)
        $empenho->solicitado_at = $sol->solicitado_at;
        $empenho->save();

        // Log
        LogSistema::create([
            'usuario_id' => Auth::id(),
            'acao' => 'pretensao_solicitada',
            'detalhes' => 'Empenho ' . $empenho->numero . ' — mes ' . $validated['mes'] . ' ano ' . $validated['ano'],
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // Notificação ao gestor
        $gestorUser = optional($empenho->contrato->gestor)->user;
        if ($gestorUser) {
            $gestorUser->notify(new PretensaoEmpenhoSubmetida($empenho, (int) $validated['mes'], (int) $validated['ano']));
        } else {
            $email = optional($empenho->contrato->gestor)->email;
            if ($email) {
                $assunto = 'Pretensão de Empenho Submetida — ' . $empenho->numero;
                $texto = 'A pretensão de empenho foi submetida para avaliação.\n'
                    . 'Contrato: ' . $empenho->contrato->numero . '\n'
                    . 'Empresa: ' . $empenho->empresa->razao_social . '\n'
                    . 'Mês/Ano: ' . $validated['mes'] . '/' . $validated['ano'] . '\n'
                    . 'Acesse: ' . url('empenhos/' . $empenho->id);
                Mail::raw($texto, function ($m) use ($email, $assunto) {
                    $m->to($email)->subject($assunto);
                });
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitação registrada e enviada para aprovação.',
            'solicitacao_id' => $sol->id,
            'solicitado_at' => optional($sol->solicitado_at)->toIso8601String(),
        ]);
    }

    /**
     * Salva solicitação via nova rota dedicada (mesmo comportamento de solicitarPretensao).
     */
    public function salvarSolicitacao(Request $request, $id)
    {
        return $this->solicitarPretensao($request, $id);
    }

    /**
     * Aprova a solicitação, gera PDF, salva em documentos e marca como aprovado.
     */
    public function aprovarSolicitacao(Request $request, $solicitacaoId)
    {
        $sol = EmpenhoSolicitacao::with(['empenho.contrato', 'empenho.empresa'])->findOrFail($solicitacaoId);
        $empenho = $sol->empenho;

        // Gera PDF de pretensão
        $mes = (int) $sol->mes;
        $ano = (int) $sol->ano;
        $mesNome = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ][$mes] ?? '';

        $pdf = Pdf::loadView('empenhos.pretensao_pdf', [
            'empenho' => $empenho,
            'mes' => $mes,
            'mesNome' => $mesNome,
            'ano' => $ano,
        ])->setPaper('a4', 'portrait');

        $safeNumero = preg_replace('/[^a-zA-Z0-9_\-]+/', '_', $empenho->numero ?? ('empenho_' . $empenho->id));
        $nomeArquivo = 'pretensao_' . $safeNumero . '_' . $mes . '-' . $ano . '_' . date('Ymd_His') . '.pdf';
        $path = 'empenhos_pretensoes/' . $nomeArquivo;
        Storage::disk('public')->put($path, $pdf->output());

        // Atualiza solicitação como aprovada
        $sol->status = 'aprovado';
        $sol->aprovado_by = Auth::id();
        $sol->aprovado_at = now();
        $sol->pdf_path = $path;
        $sol->save();

        // Registra como documento do contrato
        Documento::create([
            'contrato_id' => $empenho->contrato_id,
            'tipo' => 'OUTROS',
            'titulo' => 'Pretensão de Empenho — Nº ' . $empenho->numero . ' — ' . $mes . '/' . $ano,
            'descricao' => 'PDF gerado após aprovação da solicitação de pretensão de empenho.',
            'caminho_arquivo' => $path,
            'versao' => null,
            'data_upload' => now(),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação aprovada, PDF gerado e registrado em Documentos.',
            'pdf_path' => $path,
            'aprovado_at' => optional($sol->aprovado_at)->toIso8601String(),
        ]);
    }

    private function brToDecimal(?string $val): ?float
    {
        if ($val === null) {
            return null;
        }
        $clean = preg_replace('/[^\d,\.]/', '', $val);
        if ($clean === '' || $clean === null) {
            return null;
        }
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return is_numeric($clean) ? (float) $clean : null;
    }
}
