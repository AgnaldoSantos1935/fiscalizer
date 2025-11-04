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
    Schema::create('monitoramentos', function (Blueprint $table) {
        $table->id();
        $table->string('nome'); // Nome amigável (ex: GLPI SEDUC, Gateway PRODEPA)
        $table->string('tipo')->default('link'); // 'ip' ou 'link'
        $table->string('alvo'); // IP ou URL
        $table->integer('porta')->nullable(); // usado para IPs
        $table->boolean('ativo')->default(true);
        $table->boolean('online')->default(false);
        $table->integer('status_code')->nullable(); // usado para URLs HTTP
        $table->float('latencia')->nullable(); // tempo de resposta (ms)
        $table->text('erro')->nullable();
        $table->timestamp('ultima_verificacao')->nullable();
        $table->timestamps();
        $table->foreignId('monitoramento_id')->nullable()->constrained('monitoramentos')->nullOnDelete();

    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoramentos');
    }
};
