<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('equipamentos')) {
            Schema::create('equipamentos', function (Blueprint $table) {
                $table->id();
                $table->string('serial_number')->nullable()->index();
                $table->string('hostname')->nullable();
                $table->string('sistema_operacional')->nullable();
                $table->integer('ram_gb')->nullable();
                $table->string('cpu_resumida')->nullable();
                $table->string('ip_atual')->nullable();
                $table->json('discos')->nullable();
                $table->timestamp('ultimo_checkin')->nullable();
                $table->string('origem_inventario')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('equipamentos', function (Blueprint $table) {
                if (! Schema::hasColumn('equipamentos', 'serial_number')) {
                    $table->string('serial_number')->nullable()->index();
                }
                if (! Schema::hasColumn('equipamentos', 'hostname')) {
                    $table->string('hostname')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'sistema_operacional')) {
                    $table->string('sistema_operacional')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'ram_gb')) {
                    $table->integer('ram_gb')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'cpu_resumida')) {
                    $table->string('cpu_resumida')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'ip_atual')) {
                    $table->string('ip_atual')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'discos')) {
                    $table->json('discos')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'ultimo_checkin')) {
                    $table->timestamp('ultimo_checkin')->nullable();
                }
                if (! Schema::hasColumn('equipamentos', 'origem_inventario')) {
                    $table->string('origem_inventario')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('equipamentos')) {
            Schema::dropIfExists('equipamentos');
        }
    }
};
