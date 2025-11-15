<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\HostTeste;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Yajra\DataTables\Facades\DataTables;

class HostTesteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = HostTeste::with('host');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('host', fn($row) => $row->host->nome_conexao ?? '—')
                ->addColumn('status', function ($row) {
                    $badge = match($row->status_conexao) {
                        'ativo' => 'success',
                        'falha' => 'danger',
                        default => 'secondary'
                    };
                    return "<span class='badge bg-$badge text-uppercase px-2 py-1'>{$row->status_conexao}</span>";
                })
                ->addColumn('modo', fn($row) => ucfirst($row->modo_execucao))
                ->addColumn('acoes', fn($row) =>
                    '<a href="'.route('host_testes.show', $row->id).'" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>'
                )
                ->rawColumns(['acoes', 'status'])
                ->make(true);
        }

        return view('host_testes.index');
    }

    public function show($id)
    {
        $teste = HostTeste::with('host')->findOrFail($id);
        return view('host_testes.show', compact('teste'));
    }

    public function executarTesteManual($id)
    {
        $host = Host::findOrFail($id);
        $ip = $host->ip_atingivel;

        $comando = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            ? ['ping', '-n', '4', $ip]
            : ['ping', '-c', '4', $ip];

        $inicio = microtime(true);

        try {
            $process = new Process($comando);
            $process->setTimeout(15);
            $process->run();

            $fim = microtime(true);
            $duracao = round(($fim - $inicio) * 1000, 2);
            $saidaBruta = $process->getOutput();
            $saida = $this->sanitizeOutput($saidaBruta);

            preg_match('/time[=<]\s?(\d+\.?\d*)/i', $saidaBruta, $latencia);
            // Regex compatível com Windows e Linux para perda de pacotes
            $perda = [];
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                preg_match('/(\d+)%\s+de\s+perda|([0-9]+)%\s+loss/i', $saidaBruta, $perda);
            } else {
                preg_match('/(\d+)%\s+packet\s+loss/i', $saidaBruta, $perda);
            }

            $teste = HostTeste::create([
                'host_id'        => $host->id,
                'ip_destino'     => $ip,
                'status_conexao' => $process->isSuccessful() ? 'ativo' : 'falha',
                'latencia_ms'    => $latencia[1] ?? null,
                'perda_pacotes'  => ($perda[1] ?? $perda[2] ?? null),
                'tempo_resposta' => $duracao,
                'ip_origem'      => getHostByName(getHostName()),
                'modo_execucao'  => 'manual',
                'executado_por'  => auth()->user()->name ?? 'sistema',
                'resultado_json' => [
                    'saida_completa' => $saida,
                    'sistema' => PHP_OS,
                    'comando' => implode(' ', $comando),
                ],
            ]);

            return response()->json([
                'success'  => true,
                'mensagem' => 'Teste executado com sucesso!',
                'dados'    => $teste,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'mensagem' => 'Erro ao executar teste: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sanitiza saída garantindo UTF-8 válido e limita tamanho.
     */
    private function sanitizeOutput(?string $output): ?string
    {
        if ($output === null) return null;
        try {
            $normalized = mb_convert_encoding($output, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $normalized);
            return $clean !== false ? substr($clean, 0, 5000) : substr($output, 0, 5000);
        } catch (\Throwable $e) {
            return substr($output, 0, 5000);
        }
    }
}
