<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('escolas')) {
            return;
        }
        Schema::create('escolas', function (Blueprint $table) {
            $table->id();

            $table->string('restricao_atendimento', 57)->nullable();
            $table->string('escola', 98);
            $table->integer('codigo_inep')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('municipio', 26)->nullable();
            $table->string('localizacao', 6)->nullable();
            $table->string('localidade_diferenciada', 55)->nullable();
            $table->string('categoria_administrativa', 7)->nullable();
            $table->string('endereco', 148)->nullable();
            $table->string('telefone', 14)->nullable();
            $table->string('dependencia_administrativa', 9)->nullable();
            $table->string('categoria_escola_privada', 13)->nullable();
            $table->string('conveniada_poder_publico', 3)->nullable();
            $table->string('regulamentacao_conselho_educacao', 13)->nullable();
            $table->string('porte_escola', 44)->nullable();
            $table->string('etapas_modalidades_oferecidas', 102)->nullable();
            $table->string('outras_ofertas_educacionais', 61)->nullable();
            $table->decimal('latitude', 12, 9)->nullable();
            $table->decimal('longitude', 12, 8)->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};
