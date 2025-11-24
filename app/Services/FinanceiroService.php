<?php

namespace App\Services;

use App\Events\EmpenhoRegistrado;
use App\Events\PagamentoEfetuado;
use App\Models\Empenho;
use App\Models\Pagamentos;
use App\Models\SolicitacaoEmpenho;
use Illuminate\Support\Facades\Event;

class FinanceiroService
{
    public function criarTituloAPartirDaMedicao(int $medicaoId, int $contratoId, array $dados = []): int
    {
        $solicitacao = SolicitacaoEmpenho::create([
            'contrato_id' => $contratoId,
            'medicao_id' => $medicaoId,
            'usuario_solicitante_id' => $dados['usuario_solicitante_id'] ?? ($dados['user_id'] ?? auth()->id()),
            'numero_processo' => $dados['numero_processo'] ?? ($dados['protocolo'] ?? ''),
            'pdf_pretensao' => $dados['pdf_pretensao'] ?? null,
            'status' => $dados['status'] ?? 'solicitado',
            'observacoes' => $dados['observacoes'] ?? null,
        ]);

        return $solicitacao->id;
    }

    public function registrarEmpenho(int $solicitacaoId, array $dados, int $userId): int
    {
        $sol = SolicitacaoEmpenho::findOrFail($solicitacaoId);

        $empenho = Empenho::create([
            'numero' => $dados['numero'] ?? '',
            'contrato_id' => $sol->contrato_id,
            'empresa_id' => $dados['empresa_id'] ?? null,
            'solicitacao_empenho_id' => $sol->id,
            'medicao_id' => $sol->medicao_id,
            'processo' => $dados['processo'] ?? $sol->numero_processo,
            'data_lancamento' => $dados['data_empenho'] ?? null,
            'valor_total' => $dados['valor'] ?? 0,
            'emitido_pdf_path' => $dados['pdf_oficial'] ?? null,
        ]);

        // Atualiza o vínculo e status da solicitação
        $sol->update(['status' => 'empenhado']);

        // Auditoria
        app(AuditoriaService::class)->record(
            userId: $userId,
            action: 'financeiro.empenho_registrado',
            registroId: $empenho->id,
            modulo: 'financeiro',
            dados: ['solicitacao_empenho_id' => $sol->id]
        );

        // Dispara evento de integração
        Event::dispatch(new EmpenhoRegistrado(
            empenhoId: $empenho->id,
            solicitacaoEmpenhoId: $sol->id,
            contratoId: $sol->contrato_id,
            userId: $userId,
            dados: $dados
        ));

        return $empenho->id;
    }

    public function registrarPagamento(int $empenhoId, array $dados, int $userId): int
    {
        $pagamento = Pagamentos::create([
            'empenho_id' => $empenhoId,
            'valor_pagamento' => $dados['valor'] ?? 0,
            'data_pagamento' => $dados['data_pagamento'] ?? null,
            'documento' => $dados['arquivo_comprovante_pdf'] ?? null,
            'observacao' => $dados['observacao'] ?? null,
        ]);

        // Atualiza campos agregados no Empenho
        Empenho::where('id', $empenhoId)->update([
            'pago_comprovante_path' => $dados['arquivo_comprovante_pdf'] ?? null,
            'pago_at' => $dados['data_pagamento'] ?? now(),
        ]);

        $empenho = Empenho::find($empenhoId);
        app(AuditoriaService::class)->record(
            userId: $userId,
            action: 'financeiro.pagamento_registrado',
            registroId: $pagamento->id,
            modulo: 'financeiro',
            dados: ['empenho_id' => $empenhoId]
        );
        Event::dispatch(new PagamentoEfetuado(
            pagamentoId: $pagamento->id,
            empenhoId: $empenhoId,
            contratoId: $empenho?->contrato_id ?? 0,
            userId: $userId,
            dados: $dados
        ));

        return $pagamento->id;
    }
}
