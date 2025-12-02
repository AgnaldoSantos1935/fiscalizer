<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('contrato_itens')) {
            return;
        }
        Schema::create('contrato_itens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            $table->string('descricao_item');
            $table->string('unidade_medida')->nullable();

            $table->decimal('quantidade', 15, 2)->default(0);
            $table->integer('meses')->nullable();
            $table->decimal('valor_unitario', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->nullable();

            $table->string('tipo_item')->default('servico');
            $table->string('status')->default('ativo');

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contrato_itens');
    }
};
