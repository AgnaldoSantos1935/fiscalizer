<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\Empresa;
use App\Models\Pessoa;

use App\Models\Medicao;
use App\Models\MedicaoItem;
use App\Models\Empenho;
use App\Models\Pagamento;
use App\Models\Documento;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * ============================================================
 *  CONTROLLER OFICIAL DO MÓDULO DE CONTRATOS — FISCALIZER 2.0
 *  SUPORTE A:
 *   - BENS
 *   - SERVIÇOS COM ITENS
 *   - MEDIÇÃO MENSAL
 *   - PRETENSÃO DE EMPENHO MENSAL
 *   - PAGAMENTO MENSAL
 *   - EVENTOS E AUDITORIA
 * ============================================================
 */
class ContratoController extends Controller
{

/* ========================================================================
   INDEX (LISTAGEM)
   ======================================================================== */
public function index(Request $request)
{
    $query = Contrato::with([
        'contratada:id,razao_social,cnpj,email,representante',
        'situacaoContrato:id,nome,cor,slug'
    ]);

    if ($request->filled('numero')) {
        $query->where('numero', 'like', "%{$request->numero}%");
    }

    if ($request->filled('empresa')) {
        $query->whereHas('contratada', fn($q) =>
            $q->where('razao_social', 'like', "%{$request->empresa}%")
        );
    }

    if ($request->filled('situacao')) {
        $query->whereHas('situacaoContrato', fn($q) =>
            $q->where('slug', $request->situacao)
        );
    }

    return view('contratos.index', [
        'contratos' => $query->orderByDesc('id')->limit(500)->get(),
        'situacoes' => \App\Models\SituacaoContrato::orderBy('nome')->get(),
    ]);
}



/* ========================================================================
   STORE — CRIAR CONTRATO (Bens / Serviços)
   ======================================================================== */
public function store(Request $request)
{
    $validated = $request->validate([
        'numero' => 'required|string|max:50',
        'objeto' => 'required|string',
        'tipo_contrato' => 'required|in:bens,servicos',
        'contratada_id' => 'required|exists:empresas,id',
        'valor_global' => 'required',
        'data_inicio' => 'nullable|date',
        'data_fim' => 'nullable|date',
        'itens_fornecimento' => 'nullable',
    ]);

    DB::beginTransaction();

    try {
        $contrato = new Contrato;
        $contrato->fill($validated);
        $contrato->valor_global = $this->brToDecimal($validated['valor_global']);
        $contrato->created_by = Auth::id();
        $contrato->save();

        // Se for contrato de serviços → cadastra os itens
        if ($contrato->tipo_contrato === 'servicos' && $request->filled('itens_fornecimento')) {
            $items = $this->decodeJson($request->itens_fornecimento);
            $this->syncItens($contrato, $items);
        }

        // AUDITORIA & LOG
        Log::info("Contrato criado", ['contrato_id' => $contrato->id, 'user_id' => Auth::id()]);
        event('fiscalizer.contrato.criado', ['contrato_id' => $contrato->id]);

        DB::commit();

        return redirect()
            ->route('contratos.show', $contrato->id)
            ->with('success', 'Contrato criado com sucesso!');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Erro ao criar contrato: ' . $e->getMessage());
    }
}



/* ========================================================================
   SHOW — TELA COMPLETA DO CONTRATO
   ======================================================================== */
public function show($id)
{
    $contrato = Contrato::with([
        'contratada',
        'situacaoContrato',

        // Fiscais
        'fiscalTecnico', 'fiscalAdministrativo', 'gestor',

        // Itens
        'itens',

        // Medições
        'medicoes.itens.item',

        // Empenhos
        'empenhos.item',

        // Pagamentos
        'pagamentos',

        // Documentos
        'documentos.documentoTipo',

    ])->findOrFail($id);

    /* ------------------------------------------
       Resumo financeiro
       ------------------------------------------ */
    $valorGlobal = (float) ($contrato->valor_global ?? 0);

    $valorEmpenhado = $contrato->empenhos->sum('valor_empenhado');
    $valorPago = $contrato->pagamentos->sum('valor_pago');
    $saldo = $valorGlobal - $valorPago;

    /* ------------------------------------------
       Cálculo de vigência
       ------------------------------------------ */
    $inicio = $this->safeDate($contrato->data_inicio);
    $fim    = $this->safeDate($contrato->data_fim);

    $dias = $inicio && $fim ? now()->diffInDays($fim, false) : null;
    $vigenciaTexto = $this->vigenciaTexto($dias, $fim);

    return view('contratos.show', [
        'contrato' => $contrato,
        'itens' => $contrato->itens,

        'medicoes' => $contrato->medicoes()
            ->orderBy('ano_referencia')
            ->orderBy('mes_referencia')
            ->get(),

        'empenhos' => $contrato->empenhos()->get(),
        'pagamentos' => $contrato->pagamentos()->get(),

        'totais' => [
            'valor_global_br' => $this->br($valorGlobal),
            'valor_empenhado_br' => $this->br($valorEmpenhado),
            'valor_pago_br' => $this->br($valorPago),
            'saldo_br' => $this->br($saldo),
            'vigencia_texto' => $vigenciaTexto,
        ],
    ]);
}



/* ========================================================================
   MÉTODO: Registrar Medição Mensal
   ======================================================================== */
public function registrarMedicaoMensal(Request $request, Contrato $contrato)
{
    if ($contrato->tipo_contrato !== 'servicos') {
        return back()->with('error', 'Somente contratos de serviços possuem medições.');
    }

    $validated = $request->validate([
        'mes' => 'required|integer|min:1|max:12',
        'ano' => 'required|integer|min:2020',
        'itens' => 'required|array',
        'itens.*.item_id' => 'required|exists:contrato_itens,id',
        'itens.*.quantidade' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    $medicao = Medicao::create([
        'contrato_id' => $contrato->id,
        'mes_referencia' => $validated['mes'],
        'ano_referencia' => $validated['ano'],
        'created_by' => Auth::id()
    ]);

    foreach ($validated['itens'] as $row) {
        $item = ContratoItem::find($row['item_id']);

        MedicaoItem::create([
            'medicao_id' => $medicao->id,
            'item_id' => $item->id,
            'quantidade_executada' => $row['quantidade'],
            'valor_total' => $row['quantidade'] * $item->valor_unitario,
        ]);
    }

    Log::info("Medição mensal registrada", ['contrato' => $contrato->id, 'medicao' => $medicao->id]);
    event('fiscalizer.medicao.registrada', ['medicao_id' => $medicao->id]);

    DB::commit();

    return back()->with('success', 'Medição registrada com sucesso!');
}



/* ========================================================================
   MÉTODO: Gerar Pretensão de Empenho
   ======================================================================== */
public function gerarPretensaoEmpenho(Contrato $contrato, Medicao $medicao)
{
    if ($contrato->tipo_contrato !== 'servicos') {
        return back()->with('error', 'Contrato não é de serviços.');
    }

    DB::beginTransaction();

    foreach ($medicao->itens as $mi) {
        Empenho::create([
            'contrato_id' => $contrato->id,
            'medicao_id' => $medicao->id,
            'item_id' => $mi->item_id,
            'mes' => $medicao->mes_referencia,
            'ano' => $medicao->ano_referencia,
            'valor_empenhado' => $mi->valor_total,
        ]);
    }

    Log::info("Pretensão de empenho gerada", ['contrato' => $contrato->id, 'medicao' => $medicao->id]);
    event('fiscalizer.empenho.gerado', ['medicao_id' => $medicao->id]);

    DB::commit();

    return back()->with('success', 'Pretensão de empenho criada com sucesso!');
}



/* ========================================================================
   MÉTODO: Registrar Pagamento Mensal
   ======================================================================== */
public function registrarPagamentoMensal(Request $request, Contrato $contrato)
{
    $validated = $request->validate([
        'medicao_id' => 'required|exists:medicoes,id',
        'valor_pago' => 'required|numeric|min:0',
        'data_pagamento' => 'required|date',
    ]);

    $medicao = Medicao::findOrFail($validated['medicao_id']);

    $pag = Pagamento::create([
        'contrato_id' => $contrato->id,
        'medicao_id' => $medicao->id,
        'mes' => $medicao->mes_referencia,
        'ano' => $medicao->ano_referencia,
        'valor_pago' => $validated['valor_pago'],
        'data_pagamento' => $validated['data_pagamento'],
    ]);

    Log::info("Pagamento registrado", ['pagamento_id' => $pag->id]);
    event('fiscalizer.pagamento.registrado', ['pagamento_id' => $pag->id]);

    return back()->with('success', 'Pagamento registrado com sucesso!');
}



/* ========================================================================
   MÉTODOS AUXILIARES
   ======================================================================== */

private function br($v)
{
    return 'R$ ' . number_format((float) $v, 2, ',', '.');
}

private function brToDecimal($v)
{
    if (!$v) return 0;
    return (float) str_replace(['.', ','], ['', '.'], $v);
}

private function decodeJson($v)
{
    try { return json_decode($v, true); }
    catch (\Throwable) { return []; }
}

private function syncItens(Contrato $contrato, array $items)
{
    $contrato->itens()->delete();

    foreach ($items as $i) {
        ContratoItem::create([
            'contrato_id' => $contrato->id,
            'descricao_item' => $i['descricao'] ?? '',
            'unidade_medida' => $i['unidade'] ?? '',
            'quantidade' => $i['quantidade'] ?? 0,
            'valor_unitario' => $i['valor_unitario'] ?? 0,
        ]);
    }
}

private function safeDate($date)
{
    try { return $date ? new Carbon($date) : null; }
    catch (\Throwable) { return null; }
}

private function vigenciaTexto($dias, $fimObj)
{
    if ($dias === null) return 'Vigência não disponível';

    if ($dias >= 0)
        return "{$dias} dias restantes (até " . $fimObj->format('d/m/Y') . ")";

    return "Vencido há " . abs($dias) . " dias";
}

}
