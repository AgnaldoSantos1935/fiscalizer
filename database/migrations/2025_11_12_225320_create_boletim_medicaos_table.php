<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletins_medicao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicao_id')->constrained('medicoes')->onDelete('cascade');
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->decimal('total_pf', 10, 2)->default(0);
            $table->decimal('total_ust', 10, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->date('data_emissao')->default(now());
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletins_medicao');
    }
};
