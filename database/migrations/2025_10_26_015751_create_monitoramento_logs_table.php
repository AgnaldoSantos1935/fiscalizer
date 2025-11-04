<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoramento_logs', function (Blueprint $table) {
            $table->id();

            // 🔗 Referência ao monitoramento principal
            $table->foreignId('monitoramento_id')
                ->constrained('monitoramentos')
                ->onDelete('cascade');

            // 🔹 Dados de cada teste
            $table->boolean('online')->default(false);
            $table->integer('status_code')->nullable();
            $table->float('latencia')->nullable();
            $table->text('erro')->nullable();

            // 🔹 Data e hora da execução
            $table->timestamp('data_teste')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoramento_logs');
    }
};
