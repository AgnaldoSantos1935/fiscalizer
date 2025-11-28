<?php

namespace App\Http\Controllers;

use App\Models\BoletimMedicao;
use App\Models\Medicao;
use App\Models\MedicaoItem;
use App\Models\Projeto;
use Illuminate\Http\Request;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class BoletimMedicaoController extends Controller
{
    /**
     * Lista os boletins de medição
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BoletimMedicao::with(['medicao', 'projeto']);

            return DataTables::of($query)
                ->addColumn('projeto', fn ($b) => $b->projeto->titulo ?? '—')
                ->addColumn('mes', fn ($b) => optional($b->medicao)->mes_referencia ?? '—')
                ->editColumn('valor_total', fn ($b) => 'R$ ' . number_format($b->valor_total, 2, ',', '.'))
                ->addColumn('acoes', function ($b) {
                    return '
                        <a href="' . route('boletins.show', $b->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                        <form method="POST" action="' . route('boletins.destroy', $b->id) . '" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Excluir este boletim?\')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['acoes'])
                ->make(true);
        }

        return view('boletins.index');
    }

    /**
     * Formulário de geração de novo boletim
     */
    public function create()
    {
        $medicoes = Medicao::with('contrato')->latest()->get();
        $projetos = Projeto::orderBy('titulo')->get();

        return view('boletins.create', compact('medicoes', 'projetos'));
    }

    /**
     * Gera boletim automaticamente com base na medição
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicao_id' => 'required|exists:medicoes,id',
            'projeto_id' => 'required|exists:projetos,id',
        ]);

        $medicao = Medicao::with('itens')->find($validated['medicao_id']);
        $projeto = Projeto::find($validated['projeto_id']);

        // Busca itens de medição correspondentes ao projeto
        $itens = MedicaoItem::where('medicao_id', $medicao->id)
            ->where('projeto_id', $projeto->id)
            ->get();

        $total_pf = $itens->sum('pontos_funcao');
        $total_ust = $itens->sum('ust');
        $valor_total = $itens->sum('valor_total');

        $boletim = BoletimMedicao::create([
            'medicao_id' => $medicao->id,
            'projeto_id' => $projeto->id,
            'total_pf' => $total_pf,
            'total_ust' => $total_ust,
            'valor_total' => $valor_total,
            'data_emissao' => now(),
            'observacao' => "Gerado automaticamente a partir da medição #{$medicao->id}",
        ]);

        // Notificações: PF e UST calculados para o projeto
        notify_event('notificacoes.projetos.pf_calculado', [
            'titulo' => 'PF calculado',
            'mensagem' => "Projeto {$projeto->id}: total PF {$total_pf}",
            'valores' => ['pf' => $total_pf, 'ust' => $total_ust, 'valor' => $valor_total],
        ], $boletim);
        notify_event('notificacoes.projetos.ust_calculada', [
            'titulo' => 'UST calculada',
            'mensagem' => "Projeto {$projeto->id}: total UST {$total_ust}",
            'valores' => ['pf' => $total_pf, 'ust' => $total_ust, 'valor' => $valor_total],
        ], $boletim);

        return redirect()->route('boletins.show', $boletim->id)
            ->with('success', 'Boletim de medição gerado com sucesso!');
    }

    /**
     * Exibe boletim detalhado
     */
    public function show(BoletimMedicao $boletim)
    {
        $boletim->load(['medicao.itens', 'projeto']);

        return view('boletins.show', compact('boletim'));
    }

    /**
     * Exporta boletim em PDF
     */
    public function exportPdf($id)
    {
        $boletim = BoletimMedicao::with(['medicao.itens', 'projeto'])->findOrFail($id);
        $pdf = PDF::loadView('boletins.pdf', compact('boletim'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Boletim_Medicao_{$boletim->id}.pdf");
    }

    /**
     * Remove boletim
     */
    public function destroy(BoletimMedicao $boletim)
    {
        $boletim->delete();

        return redirect()->route('boletins.index')->with('success', 'Boletim excluído com sucesso!');
    }
}
