<?php

namespace App\Http\Controllers;

use App\Models\AtaAdesao;
use App\Models\AtaRegistroPreco;
use App\Models\Empresa;
use App\Models\Processo;
use App\Services\AtaPdfService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtaRegistroPrecoController extends Controller
{
    public function index()
    {
        $atas = AtaRegistroPreco::with(['orgaoGerenciador', 'fornecedor'])->orderByDesc('id')->get();

        return view('atas.index', compact('atas'));
    }

    public function create()
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $empresas = Empresa::orderBy('razao_social')->get();

        return view('atas.create', compact('empresas'));
    }

    public function store(Request $r)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $data = $r->validate([
            'numero' => 'required|string|max:50|unique:atas_registro_precos',
            'processo' => 'nullable|string|max:100',
            'orgao_gerenciador_id' => 'required|exists:empresas,id',
            'fornecedor_id' => 'required|exists:empresas,id',
            'objeto' => 'required|string',
            'data_publicacao' => 'nullable|date',
            'vigencia_inicio' => 'nullable|date',
            'vigencia_fim' => 'nullable|date|after_or_equal:vigencia_inicio',
            'prorroga_total_meses' => 'nullable|integer|min:0',
            'saldo_global' => 'nullable|numeric|min:0',
        ]);
        $data['created_by'] = Auth::id();
        $ata = AtaRegistroPreco::create($data);

        return redirect()->route('atas.edit', $ata->id)->with('success', 'Ata criada');
    }

    public function show(AtaRegistroPreco $ata)
    {
        $ata->load(['orgaoGerenciador', 'fornecedor', 'itens', 'adesoes']);

        return view('atas.show', compact('ata'));
    }

    public function edit(AtaRegistroPreco $ata)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $empresas = Empresa::orderBy('razao_social')->get();
        $ata->load(['orgaoGerenciador', 'fornecedor', 'adesoes']);

        return view('atas.edit', compact('ata', 'empresas'));
    }

    public function update(Request $r, AtaRegistroPreco $ata)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $data = $r->validate([
            'processo' => 'nullable|string|max:100',
            'orgao_gerenciador_id' => 'required|exists:empresas,id',
            'fornecedor_id' => 'required|exists:empresas,id',
            'objeto' => 'required|string',
            'data_publicacao' => 'nullable|date',
            'vigencia_inicio' => 'nullable|date',
            'vigencia_fim' => 'nullable|date|after_or_equal:vigencia_inicio',
            'prorroga_total_meses' => 'nullable|integer|min:0',
            'saldo_global' => 'nullable|numeric|min:0',
            'situacao' => 'nullable|in:vigente,expirada,suspensa,revogada',
        ]);
        if (array_key_exists('saldo_global', $data)) {
            $consumido = (float) $ata->saldo_consumido;
            if ((float) ($data['saldo_global'] ?? 0) < $consumido) {
                return back()->with('error', 'Saldo global não pode ser menor que o já consumido por adesões autorizadas.');
            }
        }
        $data['updated_by'] = Auth::id();
        $ata->update($data);
        $ata->updateSituacaoAutomatic();

        return back()->with('success', 'Ata atualizada');
    }

    public function destroy(AtaRegistroPreco $ata)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $ata->delete();

        return redirect()->route('atas.index')->with('success', 'Ata removida');
    }

    public function storeAdesao(Request $r, AtaRegistroPreco $ata)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $data = $r->validate([
            'orgao_adquirente_id' => 'required|exists:empresas,id',
            'justificativa' => 'required|string',
            'valor_estimado' => 'nullable',
        ]);
        $valor = $this->brToDecimal($data['valor_estimado'] ?? null) ?? 0.0;
        if ($valor > (float) $ata->saldo_disponivel) {
            return back()->with('error', 'Valor da adesão excede o saldo disponível da ata.');
        }
        $data['ata_id'] = $ata->id;
        $data['data_solicitacao'] = now()->toDateString();
        $data['created_by'] = Auth::id();
        $data['valor_estimado'] = $valor;
        $adesao = AtaAdesao::create($data);
        $processo = Processo::firstOrCreate(
            ['codigo' => 'ADESAO_ATAS'],
            [
                'nome' => 'Fluxo de Adesão a Atas de Registro de Preços',
                'descricao' => 'Cadastro por Elaborador de TR e aprovação por Gestor de Atas',
                'versao' => '1.0',
                'ativo' => true,
            ]
        );
        $workflow = app(WorkflowService::class);
        $instancia = $workflow->iniciarProcessoParaReferencia($processo, $adesao);
        $workflow->avancar($instancia, 'Cadastro de adesão concluído');

        return redirect()->route('atas.edit', $ata->id)->with('success', 'Adesão registrada');
    }

    public function gerarAutorizacaoPdf(AtaAdesao $adesao, AtaPdfService $svc)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $path = $svc->gerarAutorizacaoAdesao($adesao);

        return redirect()->back()->with('success', 'Documento gerado: '.$path);
    }

    public function atualizarStatusAdesao(Request $r, AtaAdesao $adesao)
    {
        if (! (Auth::user()?->role_id === 1)) {
            abort(403);
        }
        $data = $r->validate([
            'status' => 'required|in:solicitada,autorizada,negada,cancelada',
        ]);
        $novo = $data['status'];
        $ata = $adesao->ata()->lockForUpdate()->first();
        if ($novo === 'autorizada') {
            $consumoAtual = (float) $ata->adesoes()->where('status', 'autorizada')->sum('valor_estimado');
            $restante = (float) ($ata->saldo_global ?? 0) - $consumoAtual;
            if ((float) ($adesao->valor_estimado ?? 0) > $restante) {
                return back()->with('error', 'Saldo insuficiente para autorizar esta adesão.');
            }
        }
        $adesao->status = $novo;
        $adesao->data_decisao = now()->toDateString();
        $adesao->save();
        $instancia = $adesao->processoInstancia;
        if ($instancia) {
            app(WorkflowService::class)->avancar($instancia, 'Decisão: '.$novo);
        }
        if ($novo === 'autorizada') {
            app(AtaPdfService::class)->gerarAutorizacaoAdesao($adesao);
        }

        return back()->with('success', 'Status da adesão atualizado.');
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
