<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicao_itens_telco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');

            $table->unsignedBigInteger('escola_id')->nullable();
            $table->string('localidade')->nullable();
            $table->string('link_id')->nullable(); // código do link/circuito

            $table->decimal('uptime_percent', 5, 2)->nullable();
            $table->integer('downtime_minutos')->default(0);
            $table->integer('qtd_quedas')->default(0);

            $table->decimal('valor_mensal_contratado', 18, 2)->nullable();
            $table->decimal('valor_desconto', 18, 2)->nullable();
            $table->decimal('valor_final', 18, 2)->nullable();

            $table->json('eventos_json')->nullable(); // logs de quedas

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicao_itens_telco');
    }
};
