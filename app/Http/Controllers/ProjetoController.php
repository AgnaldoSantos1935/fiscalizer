<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\ContratoItem;
use App\Models\User;
use App\Models\DRE;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjetoController extends Controller
{
    private array $situacoes = [
        'analise'               => 'Análise',
        'planejado'             => 'Planejado',
        'em_execucao'           => 'Em execução',
        'homologacao'           => 'Homologação',
        'aguardando_pagamento'  => 'Aguardando pagamento',
        'concluido'             => 'Concluído',
        'suspenso'              => 'Suspenso',
        'cancelado'             => 'Cancelado',
    ];

    private array $prioridades = [
        'baixa'  => 'Baixa',
        'media'  => 'Média',
        'alta'   => 'Alta',
        'critica'=> 'Crítica',
    ];

    private array $tecnologias = [
        'PHP','Laravel','Node.js','React','Vue.js',
        'MySQL','PostgreSQL','MongoDB','Docker','Kubernetes','Python'
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
    // CREATE (formulário novo)
    // =========================
    public function create()
    {
        $itensContrato  = ContratoItem::orderBy('descricao')->get();
        $usuarios       = User::orderBy('name')->get();
        $dres           = DRE::orderBy('nome')->get();
        $escolas        = Escola::orderBy('nome')->get();

        $situacoes   = $this->situacoes;
        $prioridades = $this->prioridades;
        $tecnologias = $this->tecnologias;

        return view('projetos.create', compact(
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
    // STORE (salvar novo)
    // =========================
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'             => 'nullable|string|max:50|unique:projetos,codigo',
            'titulo'             => 'required|string|max:255',
            'descricao'          => 'nullable|string',
            'sistema'            => 'nullable|string|max:150',
            'modulo'             => 'nullable|string|max:150',
            'contrato_id'        => 'nullable|integer|exists:contratos,id',
            'itemcontrato_id'    => 'nullable|integer|exists:contrato_itens,id',
            'gerente_tecnico_id' => 'nullable|integer|exists:users,id',
            'gerente_adm_id'     => 'nullable|integer|exists:users,id',
            'dre_id'             => 'nullable|integer|exists:dres,id',
            'escola_id'          => 'nullable|integer|exists:escolas,id',
            'data_inicio'        => 'nullable|date',
            'data_fim'           => 'nullable|date|after_or_equal:data_inicio',
            'situacao'           => 'required|string|in:'.implode(',', array_keys($this->situacoes)),
            'prioridade'         => 'required|string|in:'.implode(',', array_keys($this->prioridades)),
            'pf_planejado'       => 'nullable|numeric|min:0',
            'ust_planejada'      => 'nullable|numeric|min:0',
            'horas_planejadas'   => 'nullable|integer|min:0',
        ]);

        // Se não vier código, gera algo tipo PROJ-2025-0001
        if (empty($data['codigo'])) {
            $data['codigo'] = $this->gerarCodigoProjeto();
        }

        $data['pf_entregue']       = 0;
        $data['ust_entregue']      = 0;
        $data['horas_registradas'] = 0;

        $projeto = Projeto::create($data);

        return redirect()
            ->route('projetos.show', $projeto->id)
            ->with('success', 'Projeto criado com sucesso.');
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

        return view('projetos.show', compact('projeto'));
    }

    // =========================
    // EDIT (form de edição)
    // =========================
    public function edit(Projeto $projeto)
    {
        $itensContrato  = ContratoItem::orderBy('descricao')->get();
        $usuarios       = User::orderBy('name')->get();
        $dres           = DRE::orderBy('nome')->get();
        $escolas        = Escola::orderBy('nome')->get();

        $situacoes   = $this->situacoes;
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
            'codigo'             => 'nullable|string|max:50|unique:projetos,codigo,'.$projeto->id,
            'titulo'             => 'required|string|max:255',
            'descricao'          => 'nullable|string',
            'sistema'            => 'nullable|string|max:150',
            'modulo'             => 'nullable|string|max:150',
            'contrato_id'        => 'nullable|integer|exists:contratos,id',
            'itemcontrato_id'    => 'nullable|integer|exists:contrato_itens,id',
            'gerente_tecnico_id' => 'nullable|integer|exists:users,id',
            'gerente_adm_id'     => 'nullable|integer|exists:users,id',
            'dre_id'             => 'nullable|integer|exists:dres,id',
            'escola_id'          => 'nullable|integer|exists:escolas,id',
            'data_inicio'        => 'nullable|date',
            'data_fim'           => 'nullable|date|after_or_equal:data_inicio',
            'situacao'           => 'required|string|in:'.implode(',', array_keys($this->situacoes)),
            'prioridade'         => 'required|string|in:'.implode(',', array_keys($this->prioridades)),
            'pf_planejado'       => 'nullable|numeric|min:0',
            'ust_planejada'      => 'nullable|numeric|min:0',
            'horas_planejadas'   => 'nullable|integer|min:0',
        ]);

        $projeto->update($data);

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
}
