<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('indisponibilidades')) {
            return; // evita criação duplicada
        }

        Schema::create('indisponibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');

            $table->timestamp('inicio')->index();  // quando caiu
            $table->timestamp('fim')->nullable()->index(); // quando voltou
            $table->integer('duracao_segundos')->nullable(); // calculado ao fechar

            $table->string('motivo')->nullable(); // opcional (erro mais comum)
            $table->text('detalhes')->nullable(); // JSON / texto livre

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indisponibilidades');
    }
};
