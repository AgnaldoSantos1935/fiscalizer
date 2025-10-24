<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('funcao_sistemas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('medicao_id')->constrained('medicoes');
        $table->enum('tipo', ['EE','SE','CE','ALI','AIE']);
        $table->string('nome_funcao', 150);
        $table->enum('complexidade', ['baixa','media','alta']);
        $table->decimal('pontos', 6, 2)->default(0);
        $table->text('justificativa')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcao_sistemas');
    }
};
