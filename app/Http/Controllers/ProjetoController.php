<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\DRE;
use App\Models\Escola;
use App\Models\Projeto;
use App\Models\User;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    private array $situacoes = [
        'analise' => 'Análise',
        'planejado' => 'Planejado',
        'em_execucao' => 'Em execução',
        'homologacao' => 'Homologação',
        'aguardando_pagamento' => 'Aguardando pagamento',
        'concluido' => 'Concluído',
        'suspenso' => 'Suspenso',
        'cancelado' => 'Cancelado',
    ];

    private array $prioridades = [
        'baixa' => 'Baixa',
        'media' => 'Média',
        'alta' => 'Alta',
        'critica' => 'Crítica',
    ];

    private array $tecnologias = [
        'PHP', 'Laravel', 'Node.js', 'React', 'Vue.js',
        'MySQL', 'PostgreSQL', 'MongoDB', 'Docker', 'Kubernetes', 'Python',
    ];

    // =========================
    // INDEX (lista de projetos)
    // =========================
    public function index(Request $request)
    {
        $q = $request->get('q');
        $situacao = $request->get('situacao');

        $query = Projeto::with('contrato');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'like', "%{$q}%")
                    ->orWhere('titulo', 'like', "%{$q}%")
                    ->orWhere('sistema', 'like', "%{$q}%")
                    ->orWhere('modulo', 'like', "%{$q}%");
            });
        }

        if ($situacao) {
            $query->where('situacao', $situacao);
        }

        $projetos = $query->orderBy('id', 'desc')->paginate(10);

        $situacoes = $this->situacoes;

        return view('projetos.index', compact('projetos', 'situacoes'));
    }

    // =========================
    // API: lista em JSON para DataTables
    // =========================
    public function getJsonProjetos(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $situacao = $request->get('situacao');

        $query = Projeto::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'like', "%{$q}%")
                    ->orWhere('titulo', 'like', "%{$q}%")
                    ->orWhere('sistema', 'like', "%{$q}%")
                    ->orWhere('modulo', 'like', "%{$q}%");
            });
        }

        if ($situacao) {
            $query->where('situacao', $situacao);
        }

        $projetos = $query
            ->select('id', 'codigo', 'titulo', 'sistema', 'modulo', 'pf_planejado', 'situacao')
            ->orderBy('codigo')
            ->get();

        return response()->json(['data' => $projetos]);
    }

    // =========================
    // CREATE (formulário novo)
    // =========================
    public function create()
    {
        $itensContrato = ContratoItem::orderBy('descricao_item')->get();
        $usuarios = User::orderBy('name')->get();
        $dres = DRE::orderBy('nome_dre')->get();
        $escolas = Escola::orderBy('nome')->get();
        $contratos = Contrato::orderBy('id')->get();

        $situacoes = $this->situacoes;
        $prioridades = $this->prioridades;
        $tecnologias = $this->tecnologias;

        return view('projetos.create', compact(
            'itensContrato',
            'usuarios',
            'dres',
            'escolas',
            'contratos',
            'situacoes',
            'prioridades',
            'tecnologias'
        ));
    }

    private function gerarCodigoProjeto(): string
    {
        $ano = date('Y');
        $seq = Projeto::whereYear('created_at', $ano)->count() + 1;

        return sprintf('PROJ-%s-%04d', $ano, $seq);
    }

    // =========================
    // SHOW (detalhes)
    // =========================
    public function show(Projeto $projeto)
    {
        $projeto->load([
            'contrato',
            'itemContrato',
            'gerenteTecnico',
            'gerenteAdm',
            'dre',
            'escola',
        ]);

        $apfs = $projeto->apfs()->orderByDesc('created_at')->get();
        $atividades = $projeto->atividades()->orderByDesc('data')->get();
        $boletins = $projeto->boletins()->orderByDesc('data_emissao')->get();
        $medicaoItens = $projeto->medicaoItens()->orderByDesc('id')->get();
        $requisitos = $projeto->requisitos()->orderBy('titulo')->get();
        $cronograma = $projeto->cronograma()->orderBy('data_inicio')->get();
        $equipe = $projeto->equipe()->with('pessoa')->orderBy('perfil')->get();

        return view('projetos.show', compact(
            'projeto', 'apfs', 'atividades', 'boletins', 'medicaoItens', 'requisitos', 'cronograma', 'equipe'
        ));
    }

    // =========================
    // EDIT (form de edição)
    // =========================
    public function edit(Projeto $projeto)
    {
        $itensContrato = ContratoItem::orderBy('descricao')->get();
        $usuarios = User::orderBy('name')->get();
        $dres = DRE::orderBy('nome')->get();
        $escolas = Escola::orderBy('nome')->get();

        $situacoes = $this->situacoes;
        $prioridades = $this->prioridades;
        $tecnologias = $this->tecnologias;

        return view('projetos.edit', compact(
            'projeto',
            'itensContrato',
            'usuarios',
            'dres',
            'escolas',
            'situacoes',
            'prioridades',
            'tecnologias'
        ));
    }

    // =========================
    // UPDATE (atualizar)
    // =========================
    public function update(Request $request, Projeto $projeto)
    {
        $data = $request->validate([
            'codigo' => 'nullable|string|max:50|unique:projetos,codigo,' . $projeto->id,
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'sistema' => 'nullable|string|max:150',
            'modulo' => 'nullable|string|max:150',
            'contrato_id' => 'nullable|integer|exists:contratos,id',
            'itemcontrato_id' => 'nullable|integer|exists:contrato_itens,id',
            'gerente_tecnico_id' => 'nullable|integer|exists:users,id',
            'gerente_adm_id' => 'nullable|integer|exists:users,id',
            'dre_id' => 'nullable|integer|exists:dres,id',
            'escola_id' => 'nullable|integer|exists:escolas,id',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'situacao' => 'required|string|in:' . implode(',', array_keys($this->situacoes)),
            'prioridade' => 'required|string|in:' . implode(',', array_keys($this->prioridades)),
            'pf_planejado' => 'nullable|numeric|min:0',
            'ust_planejada' => 'nullable|numeric|min:0',
            'horas_planejadas' => 'nullable|integer|min:0',
        ]);

        $projeto->update($data);

        // Notificação: projeto atualizado
        notify_event('notificacoes.projetos.projeto_atualizado', [
            'titulo' => 'Projeto atualizado',
            'mensagem' => "Projeto {$projeto->id} ({$projeto->titulo}) foi atualizado.",
        ], $projeto);

        return redirect()
            ->route('projetos.show', $projeto->id)
            ->with('success', 'Projeto atualizado com sucesso.');
    }

    // =========================
    // DESTROY (excluir)
    // =========================
    public function destroy(Projeto $projeto)
    {
        $projeto->delete();

        return redirect()
            ->route('projetos.index')
            ->with('success', 'Projeto excluído com sucesso.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string'],
            'contrato_id' => ['required', 'exists:contratos,id'],
            'itemcontrato_id' => ['nullable', 'exists:contrato_itens,id'],
            // ... outros campos
        ]);

        $contrato = Contrato::with('itens')->findOrFail($data['contrato_id']);

        if ($contrato->situacao !== 'Vigente') {
            return back()->withErrors(['contrato_id' => 'Contrato não está vigente.'])->withInput();
        }

        if ($contrato->itens->isEmpty()) {
            return back()->withErrors(['contrato_id' => 'Contrato não possui itens cadastrados.'])->withInput();
        }

        $item = null;
        if (! empty($data['itemcontrato_id'])) {
            $item = ContratoItem::where('contrato_id', $contrato->id)
                ->where('id', $data['itemcontrato_id'])
                ->where('ativo', true)
                ->first();

            if (! $item) {
                return back()->withErrors(['itemcontrato_id' => 'Item de contrato inválido ou inativo.'])->withInput();
            }
        }

        // Integração com o BPM: status inicial
        $data['status'] = 'em_analise'; // ou 'novo', ou algo do seu workflow

        // Se quiser herdar PF/UST base do item
        if ($item) {
            $data['pf_planejado'] = $data['pf_planejado'] ?? $item->pf_base;
            $data['ust_planejada'] = $data['ust_planejada'] ?? $item->ust_base;
        }

        $projeto = Projeto::create($data);

        // Notificação: projeto criado
        notify_event('notificacoes.projetos.projeto_criado', [
            'titulo' => 'Projeto criado',
            'mensagem' => "Projeto {$projeto->id} ({$projeto->titulo}) criado e enviado para análise.",
        ], $projeto);

        return redirect()->route('projetos.show', $projeto->id)
            ->with('success', 'Projeto criado e enviado para análise.');
    }
}
