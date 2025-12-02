<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('medicao_itens')) {
            return;
        }
        Schema::create('medicao_itens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicao_id')
                ->constrained('medicoes')
                ->onDelete('cascade');

            $table->foreignId('item_id')
                ->constrained('contrato_itens')
                ->onDelete('cascade');

            $table->decimal('quantidade_executada', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->default(0); // calculado

            $table->timestamps();

            $table->unique(['medicao_id', 'item_id'], 'medicao_item_unico');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicao_itens');
    }
};
