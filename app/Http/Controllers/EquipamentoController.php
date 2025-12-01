<?php

namespace App\Http\Controllers;

use App\Models\Dre;
use App\Models\Equipamento;
use App\Models\Escola;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EquipamentoController extends Controller
{
    /**
     * Tela de listagem (index) – apenas retorna a view.
     */
    public function index(Request $request)
    {
        $query = Equipamento::query()->with('unidade');

        if ($hostname = trim((string) $request->get('hostname'))) {
            $query->where('hostname', 'like', "%{$hostname}%");
        }
        if ($serial = trim((string) $request->get('serial'))) {
            $query->where('serial_number', 'like', "%{$serial}%");
        }
        if ($so = trim((string) $request->get('so'))) {
            $query->where('sistema_operacional', 'like', "%{$so}%");
        }
        if ($origem = trim((string) $request->get('origem'))) {
            $query->where('origem_inventario', $origem);
        }
        if ($tipo = trim((string) $request->get('tipo'))) {
            $query->where('tipo', $tipo);
        }
        if ($unidadeId = $request->get('unidade_id')) {
            $query->where('unidade_id', $unidadeId);
        }

        $equipamentos = $query->orderBy('hostname')->paginate(20)->appends($request->query());
        $unidades = Unidade::orderBy('nome')->get();

        return view('equipamentos.index', compact('equipamentos', 'unidades'));
    }

    /**
     * Tela de criação de equipamento.
     */
    public function create()
    {
        $unidades = Unidade::orderBy('nome')->get();
        $escolas = Escola::get();
        $dres = Dre::select('codigodre', 'nome_dre')->orderBy('nome_dre')->get();
        $escolasArr = $escolas->map(function ($e) {
            return [
                'id' => $e->id,
                'nome' => $e->escola ?? $e->nome ?? '',
                'municipio' => $e->municipio ?? $e->cidade ?? '',
                'dre' => $e->dre ?? null,
            ];
        })->values()->toArray();
        $dresArr = $dres->map(function ($d) {
            return [
                'codigo' => $d->codigodre,
                'nome' => $d->nome_dre,
            ];
        })->values()->toArray();
        if (empty($dresArr)) {
            $codigos = $escolas->pluck('dre')->filter()->unique()->values()->toArray();
            $dresArr = array_map(function ($c) {
                return ['codigo' => $c, 'nome' => $c];
            }, $codigos);
        }
        $unidadesArr = $unidades->map(function ($u) {
            return [
                'id' => $u->id,
                'nome' => $u->nome,
            ];
        })->values()->toArray();

        return view('equipamentos.create', compact('unidades', 'escolas', 'dres', 'escolasArr', 'dresArr', 'unidadesArr'));
    }

    /**
     * Salvar novo equipamento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:120',
            'serial_number' => 'required|string|max:120|unique:equipamentos,serial_number',
            'sistema_operacional' => 'nullable|string|max:200|required_unless:tipo,switch,roteador,outro',
            'ram_gb' => 'nullable|integer|min:1|max:512',
            'cpu_resumida' => 'nullable|string|max:200',
            'ip_atual' => 'nullable|ip',
            'discos' => 'nullable|string|max:255',
            'ultimo_checkin' => 'nullable|date',
            'origem_inventario' => 'required|in:manual,agente,importacao',
            'unidade_id' => 'required|exists:unidades,id',
            'tipo' => 'required|in:desktop,notebook,servidor,switch,roteador,outro',
            'especificacoes' => 'nullable|string',
        ]);

        // Normalizações
        $validated['hostname'] = strtoupper($validated['hostname']);
        $validated['serial_number'] = strtoupper($validated['serial_number']);

        // Criar
        Equipamento::create($validated);

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento cadastrado com sucesso.');
    }

    /**
     * Página de detalhes do equipamento.
     */
    public function show(Equipamento $equipamento)
    {
        $equipamento->load('unidade');

        // Diagnóstico
        $diag = $this->buildDiagnostico($equipamento);

        // Histórico futuro
        $historicos = collect();

        return view('equipamentos.show', [
            'equipamento' => $equipamento,
            'diagnostico' => $diag,
            'historicos' => $historicos,
        ]);
    }

    /**
     * Tela de edição
     */
    public function edit(Equipamento $equipamento)
    {
        $unidades = Unidade::orderBy('nome')->get();

        return view('equipamentos.edit', [
            'equipamento' => $equipamento,
            'unidades' => $unidades,
        ]);
    }

    /**
     * Salvar edição
     */
    public function update(Request $request, Equipamento $equipamento)
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:120',
            'serial_number' => "required|string|max:120|unique:equipamentos,serial_number,{$equipamento->id}",
            'sistema_operacional' => 'nullable|string|max:200|required_unless:tipo,switch,roteador,outro',
            'ram_gb' => 'nullable|integer|min:1|max:512',
            'cpu_resumida' => 'nullable|string|max:200',
            'ip_atual' => 'nullable|ip',
            'discos' => 'nullable|string|max:255',
            'ultimo_checkin' => 'nullable|date',
            'origem_inventario' => 'required|in:manual,agente,importacao',
            'unidade_id' => 'required|exists:unidades,id',
            'tipo' => 'required|in:desktop,notebook,servidor,switch,roteador,outro',
            'especificacoes' => 'nullable|string',
        ]);

        // Normalizações
        $validated['hostname'] = strtoupper($validated['hostname']);
        $validated['serial_number'] = strtoupper($validated['serial_number']);

        $equipamento->update($validated);

        return redirect()
            ->route('equipamentos.show', $equipamento->id)
            ->with('success', 'Equipamento atualizado com sucesso.');
    }

    /**
     * Excluir equipamento
     */
    public function destroy(Equipamento $equipamento)
    {
        $equipamento->delete();

        return redirect()
            ->route('equipamentos.index')
            ->with('success', 'Equipamento removido com sucesso.');
    }

    /**
     * API para DataTable (lista de equipamentos com filtros server-side).
     */
    public function apiIndex(Request $request)
    {
        $query = Equipamento::query()->with('unidade');

        if ($hostname = $request->get('hostname')) {
            $query->where('hostname', 'like', "%{$hostname}%");
        }

        if ($serial = $request->get('serial')) {
            $query->where('serial_number', 'like', "%{$serial}%");
        }

        if ($so = $request->get('so')) {
            $query->where('sistema_operacional', 'like', "%{$so}%");
        }

        if ($origem = $request->get('origem')) {
            $query->where('origem_inventario', $origem);
        }

        if ($tipo = $request->get('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($unidadeId = $request->get('unidade_id')) {
            $query->where('unidade_id', $unidadeId);
        }

        return response()->json([
            'data' => $query->orderBy('hostname')->get(),
        ]);
    }

    /**
     * Dashboard de equipamentos
     */
    public function dashboard()
    {
        $now = Carbon::now();

        $total = Equipamento::count();
        $ativos = Equipamento::whereNotNull('ultimo_checkin')
            ->where('ultimo_checkin', '>=', $now->copy()->subDays(30))
            ->count();
        $semCheckin = Equipamento::where(function ($q) use ($now) {
            $q->whereNull('ultimo_checkin')
                ->orWhere('ultimo_checkin', '<', $now->copy()->subDays(30));
        })->count();

        $obsoletos = Equipamento::where(function ($q) {
            $q->where('ram_gb', '<', 4)
                ->orWhere('sistema_operacional', 'like', 'Windows 7%')
                ->orWhere('sistema_operacional', 'like', 'Windows XP%');
        })->count();

        $porTipo = Equipamento::select('tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo')
            ->orderBy('tipo')
            ->get();

        $porOrigem = Equipamento::select('origem_inventario', DB::raw('COUNT(*) as total'))
            ->groupBy('origem_inventario')
            ->orderBy('origem_inventario')
            ->get();

        $porUnidade = Equipamento::select('unidade_id', DB::raw('COUNT(*) as total'))
            ->groupBy('unidade_id')
            ->with('unidade')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return view('equipamentos.dashboard', compact(
            'total', 'ativos', 'semCheckin', 'obsoletos', 'porTipo', 'porOrigem', 'porUnidade'
        ));
    }

    public function importCsv(Request $request)
    {
        $data = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('equipamentos/importacoes', 'public');

        $fullPath = Storage::disk('public')->path($path);
        $handle = fopen($fullPath, 'r');
        if (! $handle) {
            return back()->with('error', 'Falha ao abrir o arquivo CSV.');
        }

        $firstLine = fgets($handle);
        $delimiter = $this->detectDelimiter($firstLine);
        $headers = array_map('trim', str_getcsv($firstLine, $delimiter));
        $rowNum = 1;
        $created = 0;
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

            $payload = [
                'hostname' => strtoupper(trim((string) ($row['hostname'] ?? ''))),
                'serial_number' => strtoupper(trim((string) ($row['serial_number'] ?? ''))),
                'tipo' => strtolower(trim((string) ($row['tipo'] ?? ''))),
                'sistema_operacional' => trim((string) ($row['sistema_operacional'] ?? '')),
                'ram_gb' => $this->toIntNullable($row['ram_gb'] ?? null),
                'cpu_resumida' => trim((string) ($row['cpu_resumida'] ?? '')),
                'ip_atual' => trim((string) ($row['ip_atual'] ?? '')),
                'discos' => trim((string) ($row['discos'] ?? '')),
                'ultimo_checkin' => trim((string) ($row['ultimo_checkin'] ?? '')),
                'origem_inventario' => 'importacao',
                'especificacoes' => trim((string) ($row['especificacoes'] ?? '')),
            ];

            $unidadeId = $this->resolveUnidadeId(
                $row['unidade_id'] ?? null,
                $row['inventario_token'] ?? null,
                $row['unidade_nome'] ?? null,
            );
            $payload['unidade_id'] = $unidadeId;

            $validator = Validator::make($payload, [
                'hostname' => 'required|string|max:120',
                'serial_number' => 'required|string|max:120|unique:equipamentos,serial_number',
                'sistema_operacional' => 'nullable|string|max:200|required_unless:tipo,switch,roteador,outro',
                'ram_gb' => 'nullable|integer|min:1|max:512',
                'cpu_resumida' => 'nullable|string|max:200',
                'ip_atual' => 'nullable|ip',
                'discos' => 'nullable|string|max:255',
                'ultimo_checkin' => 'nullable|date',
                'origem_inventario' => 'required|in:manual,agente,importacao',
                'unidade_id' => 'required|exists:unidades,id',
                'tipo' => 'required|in:desktop,notebook,servidor,switch,roteador,outro',
                'especificacoes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'linha' => $rowNum,
                    'mensagens' => $validator->errors()->all(),
                ];

                continue;
            }

            try {
                Equipamento::create($validator->validated());
                $created++;
            } catch (\Throwable $e) {
                $errors[] = [
                    'linha' => $rowNum,
                    'mensagens' => ['Falha ao criar equipamento: ' . $e->getMessage()],
                ];
            }
        }

        fclose($handle);

        $summary = 'Importação concluída: ' . $created . ' registros criados, ' . count($errors) . ' linhas com erro.';

        return back()->with('success', $summary)->with('import_result', [
            'sucesso' => $created,
            'erros' => $errors,
        ]);
    }

    protected function detectDelimiter(string $line): string
    {
        $comma = substr_count($line, ',');
        $semicolon = substr_count($line, ';');

        return $semicolon > $comma ? ';' : ',';
    }

    protected function toIntNullable($v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        $i = (int) $v;

        return $i > 0 ? $i : null;
    }

    protected function resolveUnidadeId($unidadeId, $inventarioToken, $unidadeNome): ?int
    {
        if ($unidadeId && \is_numeric($unidadeId)) {
            $id = (int) $unidadeId;
            if (Unidade::where('id', $id)->exists()) {
                return $id;
            }
        }
        if ($inventarioToken) {
            $u = Unidade::where('inventario_token', trim((string) $inventarioToken))->first();
            if ($u) {
                return $u->id;
            }
        }
        if ($unidadeNome) {
            $u = Unidade::where('nome', trim((string) $unidadeNome))->first();
            if ($u) {
                return $u->id;
            }
        }

        return null;
    }

    /**
     * Diagnóstico de equipamento (hardware / rede)
     */
    protected function buildDiagnostico(Equipamento $e): array
    {
        $now = Carbon::now();

        if ($e->ultimo_checkin) {
            $diff = $e->ultimo_checkin->diffInDays($now);

            if ($diff <= 7) {
                $status = 'online_recente';
            } elseif ($diff <= 30) {
                $status = 'semana_passada';
            } else {
                $status = 'desatualizado';
            }
        } else {
            $status = 'nunca_reportou';
        }

        $obsoleto = false;
        if (! is_null($e->ram_gb) && $e->ram_gb < 4) {
            $obsoleto = true;
        }
        if ($e->sistema_operacional &&
            (str_starts_with($e->sistema_operacional, 'Windows 7') ||
             str_starts_with($e->sistema_operacional, 'Windows XP'))) {
            $obsoleto = true;
        }

        return [
            'status_checkin' => $status,
            'eh_obsoleto' => $obsoleto,
        ];
    }
}
