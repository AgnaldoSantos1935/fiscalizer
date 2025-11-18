<?php

namespace App\Services;

use App\Models\ProcessoInstancia;
use App\Models\ProcessoLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function iniciarProcessoParaReferencia(\App\Models\Processo $processo, \Illuminate\Database\Eloquent\Model $referencia): \App\Models\ProcessoInstancia
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($processo, $referencia) {
            if ($processo->etapas()->count() === 0) {
                $etapa1 = \App\Models\ProcessoEtapa::create([
                    'processo_id' => $processo->id,
                    'nome' => 'Cadastro por Elaborador de TR',
                    'ordem' => 1,
                    'tipo' => 'manual',
                    'prazo_horas' => 0,
                    'responsavel_tipo' => 'elaborador_tr',
                    'checklist' => [],
                    'ativa' => true,
                ]);
                $etapa2 = \App\Models\ProcessoEtapa::create([
                    'processo_id' => $processo->id,
                    'nome' => 'Aprovação por Gestor de Atas',
                    'ordem' => 2,
                    'tipo' => 'manual',
                    'prazo_horas' => 0,
                    'responsavel_tipo' => 'gestor_atas',
                    'checklist' => [],
                    'ativa' => true,
                ]);
                \App\Models\ProcessoFluxo::create([
                    'processo_id' => $processo->id,
                    'etapa_origem_id' => $etapa1->id,
                    'etapa_destino_id' => $etapa2->id,
                    'regra' => [],
                    'acao_automatica' => null,
                ]);
            }

            $instancia = \App\Models\ProcessoInstancia::create([
                'processo_id' => $processo->id,
                'referencia_type' => get_class($referencia),
                'referencia_id' => $referencia->getKey(),
                'status' => 'pendente',
                'iniciado_por' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            foreach ($processo->etapas as $etapa) {
                \App\Models\ProcessoInstanciaEtapa::create([
                    'instancia_id' => $instancia->id,
                    'etapa_id' => $etapa->id,
                    'status' => 'pendente',
                ]);
            }

            $this->iniciar($instancia);

            return $instancia;
        });
    }

    public function iniciar(ProcessoInstancia $instancia)
    {
        $instancia->update([
            'status' => 'em_execucao',
            'data_inicio' => now(),
        ]);

        // ativa a 1ª etapa (fiscal administrativo)
        $primeira = $instancia->etapas()->orderBy('etapa_id')->first();
        $primeira->update([
            'status' => 'em_execucao',
            'data_inicio' => now(),
        ]);
    }

    public function avancar(ProcessoInstancia $instancia, ?string $observacoes = null)
    {
        return DB::transaction(function () use ($instancia, $observacoes) {

            $atual = $instancia->etapaAtual();
            if (! $atual) {
                return;
            }

            // conclui etapa atual
            $atual->update([
                'status' => 'concluida',
                'data_fim' => now(),
                'observacoes' => $observacoes,
            ]);

            // pega fluxo para próxima etapa
            $fluxo = $atual->etapa->fluxosOrigem->first();

            if (! $fluxo) {
                // fim do processo
                $instancia->update([
                    'status' => 'concluido',
                    'data_fim' => now(),
                ]);

                return;
            }

            // ativa próxima etapa
            $proxima = $instancia->etapas()
                ->where('etapa_id', $fluxo->etapa_destino_id)
                ->first();

            $proxima->update([
                'status' => 'em_execucao',
                'data_inicio' => now(),
            ]);

            // log
            ProcessoLog::create([
                'instancia_id' => $instancia->id,
                'etapa_id' => $atual->etapa_id,
                'acao' => 'avancar',
                'usuario_id' => Auth::id(),
                'mensagem' => $observacoes,
            ]);
        });
    }

    public function retornarParaFiscalAdministrativo(ProcessoInstancia $instancia, string $motivo)
    {
        return DB::transaction(function () use ($instancia, $motivo) {

            $primeira = $instancia->etapas()->orderBy('id')->first();

            // encerra etapa atual
            $instancia->etapaAtual()->update([
                'status' => 'concluida',
                'data_fim' => now(),
                'observacoes' => $motivo,
            ]);

            // reabre etapa do fiscal administrativo
            $primeira->update([
                'status' => 'em_execucao',
                'data_inicio' => now(),
            ]);

            ProcessoLog::create([
                'instancia_id' => $instancia->id,
                'acao' => 'retorno',
                'mensagem' => "Retornado ao Fiscal Administrativo: $motivo",
                'usuario_id' => null,
            ]);

        });
    }
}
