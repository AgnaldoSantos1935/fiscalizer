<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escolas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('restricao_atendimento')->nullable();
            $table->string('nome', 255); // campo 'Escola'
            $table->string('codigo_inep', 20)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('municipio', 150)->nullable();
            $table->string('localizacao', 100)->nullable();
            $table->string('localidade_diferenciada', 150)->nullable();
            $table->string('categoria_administrativa', 150)->nullable();
            $table->string('endereco', 255)->nullable();
            $table->string('telefone', 30)->nullable();
            $table->string('dependencia_administrativa', 150)->nullable();
            $table->string('categoria_escola_privada', 150)->nullable();
            $table->string('conveniada_poder_publico', 150)->nullable();
            $table->string('regulamentacao_conselho', 150)->nullable();
            $table->string('porte_escola', 100)->nullable();
            $table->text('etapas_modalidades_oferecidas')->nullable();
            $table->text('outras_ofertas_educacionais')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};
