<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monitoramentos_hosts')) {
            return; // evita criação duplicada
        }

        Schema::create('monitoramentos_hosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->string('ip', 100)->nullable();
            $table->string('status', 20)->default('desconhecido');
            $table->float('tempo_resposta')->nullable(); // em ms
            $table->text('saida_ping')->nullable();
            $table->timestamp('verificado_em')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoramentos_hosts');
    }
};
