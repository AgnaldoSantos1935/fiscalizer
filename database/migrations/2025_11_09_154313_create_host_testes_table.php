<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('host_testes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('hosts')->onDelete('cascade');
            $table->dateTime('data_teste')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('ip_origem', 45)->nullable();
            $table->string('ip_destino', 45)->nullable();
            $table->enum('status_conexao', ['ativo','inativo','timeout','falha','indisponível'])->default('ativo');
            $table->decimal('latencia_ms', 8, 2)->nullable();
            $table->decimal('perda_pacotes', 5, 2)->nullable();
            $table->integer('ttl')->nullable();
            $table->string('protocolo', 20)->nullable();
            $table->integer('porta')->nullable();
            $table->decimal('tempo_resposta', 8, 2)->nullable();
            $table->text('traceroute')->nullable();
            $table->string('resolved_hostname')->nullable();
            $table->json('resultado_json')->nullable();
            $table->enum('modo_execucao', ['agendado','manual'])->default('agendado');
            $table->string('executado_por')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('host_testes');
    }
};

