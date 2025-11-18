<?php

namespace App\Http\Controllers;

use App\Models\Host;
use App\Models\Indisponibilidade;
use App\Models\Monitoramento;
use App\Notifications\HostStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class MonitoramentoController extends Controller
{
    /**
     * Painel principal de monitoramento
     */
    public function index()
    {
        // Últimos 100 registros (com host)
        $ultimos = Monitoramento::with('host')
            ->orderByDesc('ultima_verificacao')
            ->limit(100)
            ->get();

        // Total de hosts cadastrados
        $total = Host::count();

        // Hosts ONLINE considerando a última verificação de cada host
        $online = Host::whereHas('monitoramentos', function ($q) {
            $q->orderByDesc('ultima_verificacao')
                ->limit(1)
                ->where('online', 1);
        })->count();

        $offline = $total - $online;

        $resumo = compact('total', 'online', 'offline');

        return view('monitoramentos.conexoes', compact('ultimos', 'resumo'));
    }

    /**
     * Histórico em JSON para gráficos (últimos 50 logs de um host)
     */
    public function historico($hostId)
    {
        $logs = Monitoramento::where('host_id', $hostId)
            ->orderByDesc('ultima_verificacao')
            ->limit(50)
            ->get([
                'ultima_verificacao',
                'latencia',
                'online',
                'status_code',
                'download',
                'upload',
            ]);

        return response()->json($logs);
    }

    /**
     * Endpoint chamado pelo script Python para registrar um novo monitoramento
     */
    public function atualizar(Request $r)
    {
        $r->validate([
            'id' => 'required|integer|exists:hosts,id',
            'online' => 'required|integer',
            'status_code' => 'nullable|integer',
            'latencia' => 'nullable|numeric',
            'jitter' => 'nullable|numeric',
            'perda_pacotes' => 'nullable|numeric',
            'tempo_resposta' => 'nullable|numeric',
            'cpu' => 'nullable|numeric',
            'memoria_usada' => 'nullable|numeric',
            'memoria_total' => 'nullable|numeric',
            'rx_rate' => 'nullable|numeric',
            'tx_rate' => 'nullable|numeric',
            'download' => 'nullable|numeric',
            'upload' => 'nullable|numeric',
            'erro' => 'nullable|string',
            'dados_extra' => 'nullable',
            'duracao_online' => 'nullable|integer',
            'duracao_offline' => 'nullable|integer',
        ]);

        Monitoramento::create([
            'host_id' => $r->id,
            'online' => $r->online,
            'status_code' => $r->status_code,
            'latencia' => $r->latencia,
            'jitter' => $r->jitter,
            'perda_pacotes' => $r->perda_pacotes,
            'tempo_resposta' => $r->tempo_resposta,
            'cpu' => $r->cpu,
            'memoria_usada' => $r->memoria_usada,
            'memoria_total' => $r->memoria_total,
            'rx_rate' => $r->rx_rate,
            'tx_rate' => $r->tx_rate,
            'download' => $r->download,
            'upload' => $r->upload,
            'erro' => $r->erro,
            'dados_extra' => $r->dados_extra,
            'duracao_online' => $r->duracao_online ?? 0,
            'duracao_offline' => $r->duracao_offline ?? 0,
            'ultima_verificacao' => now(),
        ]);

        $host = Host::findOrFail($r->id);

        // último status ANTES desse
        $ultimo = Monitoramento::where('host_id', $host->id)
            ->orderByDesc('ultima_verificacao')
            ->first();

        $anteriorOnline = $ultimo?->online ?? null;
        $agoraOnline = (int) $r->online === 1;

        // grava o novo registro
        $monitor = Monitoramento::create([
            'host_id' => $host->id,
            'online' => $agoraOnline,
            'status_code' => $r->status_code,
            'latencia' => $r->latencia,
            'jitter' => $r->jitter,
            'perda_pacotes' => $r->perda_pacotes,
            'tempo_resposta' => $r->tempo_resposta,
            'cpu' => $r->cpu,
            'memoria_usada' => $r->memoria_usada,
            'memoria_total' => $r->memoria_total,
            'rx_rate' => $r->rx_rate,
            'tx_rate' => $r->tx_rate,
            'download' => $r->download,
            'upload' => $r->upload,
            'erro' => $r->erro,
            'dados_extra' => $r->dados_extra,
            'duracao_online' => $r->duracao_online ?? 0,
            'duracao_offline' => $r->duracao_offline ?? 0,
            'ultima_verificacao' => now(),
        ]);

        // ======== LÓGICA DE INDISPONIBILIDADE + ALERTAS ========

        // Caso 1: caiu (antes online, agora offline)
        if ($anteriorOnline === 1 && ! $agoraOnline) {

            // abre log de indisponibilidade
            Indisponibilidade::create([
                'host_id' => $host->id,
                'inicio' => now(),
                'motivo' => 'Queda detectada pelo monitoramento',
                'detalhes' => $r->erro,
            ]);

            // notifica por e-mail (pode criar um "Notifiable" específico p/ NOC)
            Notification::route('mail', 'noc@seu.dominio.gov.br')
                ->notify(new HostStatusChangedNotification($host, false, $r->erro));

            // WhatsApp – grupo NOC / plantão
            WhatsAppService::send('5591XXXXXXXXX', "⚠️ HOST OFFLINE: {$host->nome_conexao} ({$host->host_alvo})");
        }

        // Caso 2: voltou (antes offline, agora online)
        if ($anteriorOnline === 0 && $agoraOnline) {

            $aberta = Indisponibilidade::where('host_id', $host->id)
                ->whereNull('fim')
                ->orderBy('inicio', 'desc')
                ->first();

            if ($aberta) {
                $aberta->fim = now();
                $aberta->duracao_segundos = $aberta->inicio->diffInSeconds($aberta->fim);
                $aberta->save();
            }

            Notification::route('mail', 'noc@seu.dominio.gov.br')
                ->notify(new HostStatusChangedNotification($host, true));

            WhatsAppService::send('5591XXXXXXXXX', "✅ HOST ONLINE: {$host->nome_conexao} ({$host->host_alvo})");
        }

        return response()->json(['success' => true]);
    }

    public function mikrotikDashboard()
    {
        $hosts = Host::where('tipo_monitoramento', 'mikrotik')->orderBy('nome_conexao')->get();

        return view('monitoramentos.mikrotik', compact('hosts'));
    }

    public function dashboard2()
    {
        $hosts = Host::where('tipo_monitoramento', 'mikrotik')->get();

        return view('monitoramentos.dashboard2', compact('hosts'));
    }

    public function heatline()
    {
        $hosts = Host::orderBy('nome_conexao')->get();

        return view('monitoramentos.heatline', compact('hosts'));
    }

    public function apiHeatline()
    {
        // últimos 48 pontos (ex: 24h coletando 30 em 30min)
        $quantidade = 48;

        $data = Host::with(['monitoramentos' => function ($q) use ($quantidade) {
            $q->orderByDesc('ultima_verificacao')
                ->limit($quantidade);
        }])->get()->map(function ($h) use ($quantidade) {

            // preenche o array com zeros se faltar dados
            $serie = $h->monitoramentos
                ->sortBy('ultima_verificacao')
                ->pluck('online')
                ->padLeft($quantidade, 0) // 0 = offline
                ->values();

            return [
                'host' => $h->nome_conexao,
                'values' => $serie,
            ];
        });

        return response()->json($data);
    }

    public function matrix()
    {
        $dres = DRE::orderBy('nome')->get();

        return view('monitoramentos.matrix', compact('dres'));
    }

    public function apiMatrix()
    {
        $dres = DRE::all();
        $matrix = [];

        foreach ($dres as $origem) {

            $linha = [];

            foreach ($dres as $destino) {

                if ($origem->id === $destino->id) {
                    $linha[] = null;

                    continue;
                }

                // hosts da origem que respondem ao destino
                $hosts = Host::where('local', $origem->id)
                    ->with(['monitoramentos' => function ($q) {
                        $q->latest()->limit(10);
                    }])->get();

                $latencias = [];

                foreach ($hosts as $h) {
                    foreach ($h->monitoramentos as $m) {
                        if ($m->latencia) {
                            $latencias[] = $m->latencia;
                        }
                    }
                }

                $media = count($latencias) ? round(array_sum($latencias) / count($latencias), 1) : null;

                $linha[] = $media;
            }

            $matrix[] = $linha;
        }

        return response()->json([
            'dres' => $dres->pluck('nome'),
            'matrix' => $matrix,
        ]);
    }
}
