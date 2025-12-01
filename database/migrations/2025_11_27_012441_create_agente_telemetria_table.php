<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agente_telemetria', function (Blueprint $table) {
            $table->id();
            if (Schema::hasTable('unidades')) {
                $table->foreignId('unidade_id')->constrained('unidades')->onDelete('cascade');
            } else {
                $table->unsignedBigInteger('unidade_id')->nullable()->index();
            }
            $table->string('agent_key');
            $table->string('agent_version');
            $table->decimal('cpu_usage', 5, 2);
            $table->decimal('ram_used', 5, 2);
            $table->string('internet_status');
            $table->integer('latency_ms')->nullable();
            $table->integer('agent_uptime');
            $table->integer('system_uptime');
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agente_telemetria');
    }
};
