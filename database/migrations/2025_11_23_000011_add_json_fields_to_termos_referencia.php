<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->text('fundamentacao_legal_texto')->nullable();
            $table->decimal('habilitacao_tecnica_percentual_minimo', 5, 2)->nullable();
            $table->text('habilitacao_tecnica_documentos')->nullable();
            $table->boolean('subcontratacao_permitida')->nullable();
            $table->text('subcontratacao_excecao')->nullable();
            $table->text('penalidades')->nullable();
            $table->string('assin_elaboracao')->nullable();
            $table->string('assin_supervisor')->nullable();
            $table->string('assin_ordenador_despesas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('termos_referencia', function (Blueprint $table) {
            $table->dropColumn([
                'fundamentacao_legal_texto',
                'habilitacao_tecnica_percentual_minimo',
                'habilitacao_tecnica_documentos',
                'subcontratacao_permitida',
                'subcontratacao_excecao',
                'penalidades',
                'assin_elaboracao',
                'assin_supervisor',
                'assin_ordenador_despesas',
            ]);
        });
    }
};
