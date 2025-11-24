<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empenho_solicitacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empenho_id');
            $table->unsignedBigInteger('contrato_id');
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedTinyInteger('mes');
            $table->unsignedSmallInteger('ano');
            $table->string('periodo_referencia')->nullable();
            $table->text('observacoes')->nullable();
            $table->json('dados')->nullable();
            $table->string('status')->default('pendente');
            $table->unsignedBigInteger('solicitado_by')->nullable();
            $table->timestamp('solicitado_at')->nullable();
            $table->unsignedBigInteger('aprovado_by')->nullable();
            $table->timestamp('aprovado_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empenho_id')->references('id')->on('empenhos')->onDelete('cascade');
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empenho_solicitacoes');
    }
};
