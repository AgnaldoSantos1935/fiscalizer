<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('atas_registro_precos');
        Schema::create('atas_registro_precos', function (Blueprint $t) {
            $t->id();
            $t->string('numero')->unique();
            $t->string('processo')->nullable();
            $t->foreignId('orgao_gerenciador_id')->nullable()->constrained('empresas')->nullOnDelete();
            $t->foreignId('fornecedor_id')->nullable()->constrained('empresas')->nullOnDelete();
            $t->text('objeto');
            $t->date('data_publicacao')->nullable();
            $t->date('vigencia_inicio')->nullable();
            $t->date('vigencia_fim')->nullable();
            $t->enum('situacao', ['vigente', 'expirada', 'suspensa', 'revogada'])->default('vigente');
            $t->unsignedInteger('prorroga_total_meses')->nullable();
            $t->json('prorroga_json')->nullable();
            $t->decimal('saldo_global', 18, 2)->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atas_registro_precos');
    }
};
