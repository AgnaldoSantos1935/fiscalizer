<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('equipamento_ocorrencias')) {
            Schema::create('equipamento_ocorrencias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('equipamento_id')->constrained('equipamentos')->cascadeOnDelete();
                $table->string('tipo');
                $table->text('descricao')->nullable();
                $table->string('status')->default('aberta');
                $table->timestamp('resolvida_em')->nullable();
                $table->unsignedBigInteger('reportado_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_ocorrencias');
    }
};

