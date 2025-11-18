<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('documento_tipo_id')->nullable()->after('tipo');
            $table->foreign('documento_tipo_id')->references('id')->on('documento_tipos')->onDelete('set null');
        });

        // Backfill: map existing string 'tipo' to documento_tipo_id
        $map = DB::table('documento_tipos')->pluck('id', 'slug');

        $documentos = DB::table('documentos')->select('id', 'tipo')->get();
        foreach ($documentos as $doc) {
            $slug = $doc->tipo;
            $tipoId = $map[$slug] ?? null;
            // Heurística: se 'termo_aditivo', assume 'termo_aditivo_prazo' para permitir nova_data_fim
            if (! $tipoId && $slug === 'termo_aditivo') {
                $tipoId = $map['termo_aditivo_prazo'] ?? null;
            }
            if ($tipoId) {
                DB::table('documentos')->where('id', $doc->id)->update(['documento_tipo_id' => $tipoId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['documento_tipo_id']);
            $table->dropColumn('documento_tipo_id');
        });
    }
};
