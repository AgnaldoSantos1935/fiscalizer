<?php

namespace App\Services;

use App\Models\ProcessoInstancia;
use App\Models\ProcessoInstanciaEtapa;
use App\Models\ProcessoLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function iniciar(ProcessoInstancia $instancia)
    {
        $instancia->update([
            'status'      => 'em_execucao',
            'data_inicio' => now(),
        ]);

        // ativa a 1ª etapa (fiscal administrativo)
        $primeira = $instancia->etapas()->orderBy('etapa_id')->first();
        $primeira->update([
            'status'      => 'em_execucao',
            'data_inicio' => now(),
        ]);
    }

    public function avancar(ProcessoInstancia $instancia, ?string $observacoes = null)
    {
        return DB::transaction(function () use ($instancia, $observacoes) {

            $atual = $instancia->etapaAtual();
            if (!$atual) return;

            // conclui etapa atual
            $atual->update([
                'status'      => 'concluida',
                'data_fim'    => now(),
                'observacoes' => $observacoes
            ]);

            // pega fluxo para próxima etapa
            $fluxo = $atual->etapa->fluxosOrigem->first();

            if (!$fluxo) {
                // fim do processo
                $instancia->update([
                    'status'   => 'concluido',
                    'data_fim' => now(),
                ]);
                return;
            }

            // ativa próxima etapa
            $proxima = $instancia->etapas()
                ->where('etapa_id', $fluxo->etapa_destino_id)
                ->first();

            $proxima->update([
                'status'      => 'em_execucao',
                'data_inicio' => now(),
            ]);

            // log
            ProcessoLog::create([
                'instancia_id' => $instancia->id,
                'etapa_id'     => $atual->etapa_id,
                'acao'         => 'avancar',
                'usuario_id'   => Auth::id(),
                'mensagem'     => $observacoes
            ]);
        });
    }

    public function retornarParaFiscalAdministrativo(ProcessoInstancia $instancia, string $motivo)
    {
        return DB::transaction(function () use ($instancia, $motivo) {

            $primeira = $instancia->etapas()->orderBy('id')->first();

            // encerra etapa atual
            $instancia->etapaAtual()->update([
                'status'      => 'concluida',
                'data_fim'    => now(),
                'observacoes' => $motivo,
            ]);

            // reabre etapa do fiscal administrativo
            $primeira->update([
                'status'      => 'em_execucao',
                'data_inicio' => now()
            ]);

            ProcessoLog::create([
                'instancia_id' => $instancia->id,
                'acao'         => 'retorno',
                'mensagem'     => "Retornado ao Fiscal Administrativo: $motivo",
                'usuario_id'   => null
            ]);

        });
    }
}
