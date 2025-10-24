<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('medicao', function (Blueprint $table) {
        $table->id();
        $table->foreignId('contrato_id')->constrained('contratos');
        $table->string('mes_referencia', 7);
        $table->decimal('total_pf', 10, 2)->default(0);
        $table->decimal('valor_unitario_pf', 10, 2)->default(0);
        $table->decimal('valor_total', 14, 2)->default(0);
        $table->date('data_envio')->nullable();
        $table->enum('status', ['pendente','em_analise','aprovado','reprovado'])->default('pendente');
        $table->text('observacao')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicao');
    }
};
