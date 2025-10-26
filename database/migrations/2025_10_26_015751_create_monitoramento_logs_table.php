<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

    Schema::create('monitoramento_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('monitoramento_id')->constrained()->onDelete('cascade');
        $table->boolean('online')->default(false);
        $table->integer('status_code')->nullable();
        $table->float('latencia')->nullable();
        $table->text('erro')->nullable();
        $table->timestamp('verificado_em');
        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoramento_logs');
    }
};
