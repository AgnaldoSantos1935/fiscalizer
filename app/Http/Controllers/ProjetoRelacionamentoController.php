<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use Illuminate\Http\Request;

class ProjetoRelacionamentoController extends Controller
{
    public function requisitos(Projeto $projeto)
    {
        return response()->json($projeto->funcoes);
    }

    public function atividades(Projeto $projeto)
    {
        return response()->json($projeto->atividades);
    }

    public function cronograma(Projeto $projeto)
    {
        return response()->json($projeto->cronograma);
    }

    public function equipe(Projeto $projeto)
    {
        return response()->json($projeto->equipe()->with('pessoa')->get());
    }
}
