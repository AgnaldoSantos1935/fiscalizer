<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicao_notas_fiscais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');
            $table->string('chave');                // 44 dígitos NFe
            $table->string('numero');               // número da nota
            $table->string('cnpj_prestador');
            $table->string('cnpj_tomador')->nullable();
            $table->decimal('valor', 12, 2);
            $table->string('tipo');                 // NFe, NFSe
            $table->string('status');               // pendente, valido, invalido, erro
            $table->text('mensagem')->nullable();
            $table->json('retorno_api')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicao_notas_fiscais');
    }
};
