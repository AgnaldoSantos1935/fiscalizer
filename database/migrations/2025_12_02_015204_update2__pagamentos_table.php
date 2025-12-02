<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('pagamentos')) {
            return;
        }
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contrato_id')
                ->constrained('contratos')
                ->onDelete('cascade');

            $table->foreignId('medicao_id')
                ->nullable()
                ->constrained('medicoes')
                ->onDelete('cascade');

            $table->integer('mes')->nullable();
            $table->integer('ano')->nullable();

            $table->decimal('valor_pago', 15, 2)->default(0);

            $table->date('data_pagamento')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagamentos');
    }
};
