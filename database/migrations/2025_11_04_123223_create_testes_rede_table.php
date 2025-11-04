<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testes_rede', function (Blueprint $table) {
            $table->id();
            $table->string('alvo', 255);
            $table->enum('tipo', ['IP', 'Domínio']);
            $table->string('dns')->nullable();
            $table->string('ping')->nullable();
            $table->integer('http_status')->nullable();
            $table->boolean('http_ok')->default(false);
            $table->text('http_erro')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('data_teste')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testes_rede');
    }
};
