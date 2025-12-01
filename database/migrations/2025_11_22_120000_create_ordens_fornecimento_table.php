<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ordens_fornecimento')) {
            Schema::create('ordens_fornecimento', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('contrato_id');
                $table->unsignedBigInteger('empenho_id');
                $table->string('numero_of');
                $table->unsignedInteger('ano_of');
                $table->dateTime('data_emissao')->nullable();
                $table->string('arquivo_pdf')->nullable();
                $table->json('itens_json')->nullable();
                $table->string('assinatura_hash')->nullable();
                $table->string('verificacao_url')->nullable();
                $table->timestamps();

                $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
                $table->foreign('empenho_id')->references('id')->on('empenhos')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_fornecimento');
    }
};
