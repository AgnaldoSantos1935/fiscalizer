<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('termo_referencia_logs')) {
            Schema::create('termo_referencia_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('termo_referencia_id')->constrained('termos_referencia')->onDelete('cascade');
                $table->string('acao'); // enviar_aprovacao, aprovar, reprovar, retornar
                $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('motivo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('termo_referencia_logs');
    }
};
