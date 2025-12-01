<?php

namespace App\Http\Controllers;

use App\Models\Dre;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class EscolaController extends Controller
{
    /**
     * Exibe a página principal com DataTable.
     */
    public function index()
    {
        return view('escolas.index');
    }

    /**
     * Endpoint para DataTables (JSON)
     */
    /* public function getData(Request $request)
     {
         if ($request->ajax()) {
             $escolas = Escola::select([
                 'id as id_escola',
                 'codigo_inep',
                 'escola',
                 'municipio',
                 'uf',
                 'dre',
                 'telefone'
             ]);

             return DataTables::of($escolas)
                 ->addColumn('dre_nome', fn($row) => $row->dre ?? '-')
                 ->make(true);
         }

         abort(404);
     }*/

    public function getData(Request $request)
    {
        $query = Escola::query();

        // 🔍 Filtros opcionais (compatíveis com UI)
        if ($request->filled('codigo')) {
            $codigo = trim((string) $request->codigo);
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                $query->whereRaw('CAST(codigo_inep AS TEXT) LIKE ?', ['%' . $codigo . '%']);
            } else {
                $query->whereRaw('CAST(codigo_inep AS CHAR) LIKE ?', ['%' . $codigo . '%']);
            }
        }
        if ($request->filled('nome')) {
            $nome = '%' . trim((string) $request->nome) . '%';
            $query->where(function ($q) use ($nome) {
                $q->orWhere('escola', 'like', $nome);
                if (Schema::hasColumn('escolas', 'nome')) {
                    $q->orWhere('nome', 'like', $nome);
                }
            });
        }
        if ($request->filled('municipio')) {
            $query->where('municipio', 'like', '%' . trim((string) $request->municipio) . '%');
        }
        if ($request->filled('uf')) {
            $query->where('uf', strtoupper(trim((string) $request->uf)));
        }

        $select = ['id as id_escola', 'codigo_inep', 'municipio', 'uf'];
        $hasEscola = Schema::hasColumn('escolas', 'escola');
        $hasNome = Schema::hasColumn('escolas', 'nome');
        if ($hasEscola && $hasNome) {
            $select[] = DB::raw('COALESCE(escola, nome) as escola');
        } elseif ($hasEscola) {
            $select[] = 'escola';
        } elseif ($hasNome) {
            $select[] = DB::raw('nome as escola');
        } else {
            $select[] = DB::raw('NULL as escola');
        }
        if (Schema::hasColumn('escolas', 'dre')) {
            $select[] = 'dre';
        } else {
            $select[] = DB::raw('NULL as dre');
        }

        $orderExpr = $hasEscola && $hasNome ? 'COALESCE(escola, nome)' : ($hasEscola ? 'escola' : ($hasNome ? 'nome' : 'id'));

        $escolas = $query
            ->select($select)
            ->orderByRaw($orderExpr . ' asc')
            ->limit(500)
            ->get();

        return response()->json(['data' => $escolas]);
    }

    /**
     * Retorna JSON para o modal de detalhes (usado pelo fetch).
     */
    public function show($id_escola)
    {
        $escola = Escola::findOrFail($id_escola);

        return response()->json(['escola' => $escola]);
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        $dres = Dre::all();

        return view('escolas.create', compact('dres'));
    }

    /**
     * Importa escolas via CSV/Texto, incluindo coluna dre.
     */
    public function importCsv(Request $request)
    {
        $data = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:20480',
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('escolas/importacoes', 'public');
        $fullPath = \Storage::disk('public')->path($path);

        $handle = fopen($fullPath, 'r');
        if (! $handle) {
            return back()->with('error', 'Falha ao abrir o arquivo CSV.');
        }

        $firstLine = fgets($handle);
        $delimiter = $this->detectDelimiter($firstLine);
        $headers = array_map('trim', str_getcsv($firstLine, $delimiter));

        $rowNum = 1;
        $created = 0;
        $updated = 0;
        $errors = [];

        while (($line = fgets($handle)) !== false) {
            $rowNum++;
            $cols = str_getcsv($line, $delimiter);
            if (count($cols) === 1 && trim($cols[0]) === '') {
                continue;
            }

            $row = [];
            foreach ($headers as $i => $h) {
                $row[$h] = $cols[$i] ?? null;
            }

            $codigo = trim((string) ($row['codigo_inep'] ?? ''));
            $nome = trim((string) ($row['escola'] ?? ($row['nome'] ?? '')));
            $municipio = trim((string) ($row['municipio'] ?? ($row['cidade'] ?? '')));
            $uf = strtoupper(trim((string) ($row['uf'] ?? '')));
            $dreVal = trim((string) ($row['dre'] ?? ($row['codigodre'] ?? ($row['dre_codigo'] ?? ''))));

            $dreCodigo = $this->resolveDreCodigo($dreVal);

            try {
                $escola = null;
                if ($codigo) {
                    $escola = Escola::where('codigo_inep', $codigo)->first();
                }
                if (! $escola && $nome) {
                    $q = Escola::where(function ($q) use ($nome) {
                        $q->where('escola', $nome)->orWhere('nome', $nome);
                    });
                    if ($municipio) {
                        $q->where('municipio', $municipio);
                    }
                    if ($uf) {
                        $q->where('uf', $uf);
                    }
                    $escola = $q->first();
                }

                $payload = [
                    'codigo_inep' => $codigo ?: ($escola->codigo_inep ?? null),
                    'escola' => $nome ?: ($escola->escola ?? $escola->nome ?? null),
                    'municipio' => $municipio ?: ($escola->municipio ?? null),
                    'uf' => $uf ?: ($escola->uf ?? null),
                    'dre' => $dreCodigo,
                ];

                if ($escola) {
                    $escola->update($payload);
                    $updated++;
                } else {
                    Escola::create($payload);
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors[] = ['linha' => $rowNum, 'mensagens' => [$e->getMessage()]];
            }
        }

        fclose($handle);

        $summary = "Escolas importadas: {$created} criadas, {$updated} atualizadas, " . count($errors) . ' linhas com erro.';

        return back()->with('success', $summary)->with('escolas_import_result', [
            'criados' => $created,
            'atualizados' => $updated,
            'erros' => $errors,
        ]);
    }

    protected function detectDelimiter(string $line): string
    {
        $comma = substr_count($line, ',');
        $semicolon = substr_count($line, ';');
        $tab = substr_count($line, "\t");
        if ($tab > max($comma, $semicolon)) {
            return "\t";
        }

        return $semicolon > $comma ? ';' : ',';
    }

    protected function resolveDreCodigo(?string $val): ?string
    {
        $s = trim((string) $val);
        if ($s === '') {
            return null;
        }
        $sNorm = strtoupper($s);
        $dre = Dre::where('codigodre', $sNorm)->first();
        if ($dre) {
            return $dre->codigodre;
        }
        $dre2 = Dre::where('nome_dre', 'ilike', $s)->first();

        return $dre2 ? $dre2->codigodre : $sNorm;
    }

    /**
     * Armazena nova escola.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_inep' => 'required|string|max:20|unique:escolas,codigo_inep',
            'escola' => 'required|string|max:255',
        ]);
        $logradouro = trim($request->input('logradouro', ''));
        $numero = trim($request->input('numero', ''));
        $complemento = trim($request->input('complemento', ''));
        $bairro = trim($request->input('bairro', ''));
        $endereco = $request->input('endereco');
        if ($logradouro) {
            $parts = [];
            $parts[] = $logradouro;
            if ($numero) {
                $parts[] = $numero;
            }
            if ($complemento) {
                $parts[] = $complemento;
            }
            if ($bairro) {
                $parts[] = $bairro;
            }
            $endereco = implode(', ', $parts);
        }
        $data = $validated + $request->except('_token');
        if ($endereco) {
            $data['endereco'] = $endereco;
        }
        Escola::create($data);

        return redirect()->route('escolas.index')->with('success', 'Escola cadastrada com sucesso!');
    }

    /**
     * Formulário de edição.
     */
    public function edit(Escola $escola)
    {

        return view('escolas.edit', compact('escola'));
    }

    /**
     * Atualiza uma escola.
     */
    public function update(Request $request, Escola $escola)
    {
        $validated = $request->validate([
            'codigo_inep' => 'required|string|max:20|unique:escolas,codigo_inep,' . $escola->id,
            'escola' => 'required|string|max:255',
        ]);
        $logradouro = trim($request->input('logradouro', ''));
        $numero = trim($request->input('numero', ''));
        $complemento = trim($request->input('complemento', ''));
        $bairro = trim($request->input('bairro', ''));
        $endereco = $request->input('endereco');
        if ($logradouro) {
            $parts = [];
            $parts[] = $logradouro;
            if ($numero) {
                $parts[] = $numero;
            }
            if ($complemento) {
                $parts[] = $complemento;
            }
            if ($bairro) {
                $parts[] = $bairro;
            }
            $endereco = implode(', ', $parts);
        }
        $data = $validated + $request->except(['_token', '_method']);
        if ($endereco) {
            $data['endereco'] = $endereco;
        }
        $escola->update($data);

        return redirect()->route('escolas.index')->with('success', 'Escola atualizada com sucesso!');
    }

    /**
     * Exclui uma escola.
     */
    public function destroy(Escola $escola)
    {
        $escola->delete();

        return response()->json(['success' => true]);
    }
}
