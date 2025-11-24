<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            if (! Schema::hasColumn('empenhos', 'solicitacao_empenho_id')) {
                $table->foreignId('solicitacao_empenho_id')
                    ->nullable()
                    ->constrained('solicitacoes_empenho')
                    ->nullOnDelete()
                    ->after('contrato_id');
            }
            if (! Schema::hasColumn('empenhos', 'medicao_id')) {
                $table->foreignId('medicao_id')
                    ->nullable()
                    ->constrained('medicoes')
                    ->nullOnDelete()
                    ->after('solicitacao_empenho_id');
            }
            if (! Schema::hasColumn('empenhos', 'emitido_pdf_path')) {
                $table->string('emitido_pdf_path')->nullable()->after('valor_extenso');
            }
            if (! Schema::hasColumn('empenhos', 'emitido_at')) {
                $table->timestamp('emitido_at')->nullable()->after('emitido_pdf_path');
            }
            if (! Schema::hasColumn('empenhos', 'pago_comprovante_path')) {
                $table->string('pago_comprovante_path')->nullable()->after('emitido_at');
            }
            if (! Schema::hasColumn('empenhos', 'pago_at')) {
                $table->timestamp('pago_at')->nullable()->after('pago_comprovante_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            if (Schema::hasColumn('empenhos', 'solicitacao_empenho_id')) {
                $table->dropConstrainedForeignId('solicitacao_empenho_id');
            }
            if (Schema::hasColumn('empenhos', 'medicao_id')) {
                $table->dropConstrainedForeignId('medicao_id');
            }
            foreach (['emitido_pdf_path', 'emitido_at', 'pago_comprovante_path', 'pago_at'] as $col) {
                if (Schema::hasColumn('empenhos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
