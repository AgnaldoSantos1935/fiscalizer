<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notas_empenho_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nota_empenho_id');
            $table->integer('item_numero')->nullable();
            $table->string('descricao', 500);
            $table->string('unidade', 50)->nullable();
            $table->decimal('quantidade', 12, 2)->default(0);
            $table->decimal('valor_unitario', 14, 2)->default(0);
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nota_empenho_id')
                ->references('id')
                ->on('notas_empenho')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notas_empenho_itens');
    }
};
