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
       Schema::create('medicao_itens_software', function (Blueprint $table) {
    $table->id();
    $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');

    $table->unsignedBigInteger('demanda_id')->nullable();
    $table->unsignedBigInteger('os_id')->nullable(); // ordem de serviço
    $table->string('sistema')->nullable();
    $table->string('modulo')->nullable();

    $table->string('descricao');
    $table->integer('pf')->default(0);
    $table->integer('ust')->default(0);
    $table->decimal('horas', 8, 2)->default(0);
    $table->integer('qtd_pessoas')->default(0);

    $table->decimal('valor_unitario_pf', 18, 2)->nullable();
    $table->decimal('valor_unitario_ust', 18, 2)->nullable();
    $table->decimal('valor_total', 18, 2)->nullable();

    $table->string('hash_unico', 64)->index(); // prevenção de duplicidade

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicao_itens_software');
    }
};
