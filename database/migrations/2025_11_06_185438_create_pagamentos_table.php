<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('pagamentos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('empenho_id');
        $table->decimal('valor_pagamento', 15, 2);
        $table->date('data_pagamento')->nullable();
        $table->string('documento', 50)->nullable(); // ex: número da nota ou OB
        $table->string('observacao')->nullable();
        $table->timestamps();

        $table->foreign('empenho_id')->references('id')->on('empenhos')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
