<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_empenho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->foreignId('medicao_id')->nullable()->constrained('medicoes')->nullOnDelete();
            $table->foreignId('usuario_solicitante_id')->constrained('users')->restrictOnDelete();
            $table->string('numero_processo')->index();
            $table->string('pdf_pretensao')->nullable();
            $table->string('status')->default('solicitado')->index();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_empenho');
    }
};
