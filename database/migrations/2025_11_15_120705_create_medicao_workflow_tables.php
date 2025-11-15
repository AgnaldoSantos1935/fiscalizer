<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Documentos anexados pela empresa e pela fiscalização
        Schema::create('medicao_documentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');
            $table->string('tipo'); // planilha, relatorio_execucao, foto, nf, certidao...
            $table->string('arquivo');
            $table->timestamps();
        });

        // Atesto dos serviços
        Schema::create('medicao_atestos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');
            $table->foreignId('fiscal_id')->constrained('users');

            $table->text('observacoes')->nullable();
            $table->timestamp('data_assinatura');
            $table->string('hash_assinatura');            // hash único
            $table->string('arquivo_pdf')->nullable();   // PDF gerado

            $table->timestamps();
        });

        // Registros de envio ao sistema externo
        Schema::create('medicao_integracoes_externas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');
            $table->string('sistema'); // ex: SEI, SIPAC, SIGAD
            $table->string('status');
            $table->string('numero_processo')->nullable();
            $table->text('mensagem')->nullable();
            $table->timestamp('data_envio')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicao_integracoes_externas');
        Schema::dropIfExists('medicao_atestos');
        Schema::dropIfExists('medicao_documentos');
    }
};
