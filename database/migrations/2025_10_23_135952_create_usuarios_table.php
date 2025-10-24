<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

   public function up(): void
{
    Schema::create('usuarios', function (Blueprint $table) {
        $table->id();
        $table->string('nome', 150);
        $table->string('cargo', 100)->nullable();
        $table->enum('perfil', ['gestor','fiscal','analista','admin'])->default('analista');
        $table->string('email', 150)->nullable();
        $table->string('senha_hash', 255)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
