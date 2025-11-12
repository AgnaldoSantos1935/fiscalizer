<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipes_projeto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');
            $table->string('papel')->nullable();
            $table->decimal('horas_previstas', 8, 2)->default(0);
            $table->decimal('horas_realizadas', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('equipes_projeto');
    }
};
