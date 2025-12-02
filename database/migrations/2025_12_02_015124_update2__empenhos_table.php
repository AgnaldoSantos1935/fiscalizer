<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('empenhos')) {
            return;
        }
        Schema::create('empenhos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            $table->foreignId('medicao_id')
                ->nullable()
                ->constrained('medicoes')
                ->onDelete('cascade');

            $table->foreignId('item_id')
                ->nullable()
                ->constrained('contrato_itens')
                ->onDelete('cascade');

            $table->string('numero')->nullable();

            $table->integer('mes')->nullable();
            $table->integer('ano')->nullable();

            $table->decimal('valor_empenhado', 15, 2)->default(0);

            $table->string('status')->default('pendente'); // pendente / liquidado / pago etc.

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('empenhos');
    }
};
