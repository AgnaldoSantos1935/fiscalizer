<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('monitoramento_ips', function (Blueprint $table) {
        $table->id();
        $table->string('nome'); // Ex: "Servidor GLPI"
        $table->string('endereco_ip'); // Ex: "10.10.1.5"
        $table->integer('porta')->nullable(); // opcional (ex: 80, 443, 22)
        $table->boolean('ativo')->default(true);
        $table->boolean('online')->default(false);
        $table->float('latencia')->nullable(); // tempo de resposta (ms)
        $table->text('erro')->nullable();
        $table->timestamp('ultima_verificacao')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoramento_ips');
    }
};
