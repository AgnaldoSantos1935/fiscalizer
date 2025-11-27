<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            $driver = DB::connection()->getDriverName();
        } catch (\Throwable $e) {
            $driver = null;
        }

        if ($driver !== 'pgsql') {
            return; // apenas PostgreSQL suporta pgvector
        }

        // Habilita extensão pgvector (se disponível)
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
        } catch (\Throwable $e) {
            return; // não interrompe deploy caso a extensão não esteja instalada
        }

        // Adiciona coluna vector(128) se não existir
        $exists = DB::select("SELECT 1 FROM information_schema.columns WHERE table_name = 'normas_trechos' AND column_name = 'embedding_vec' LIMIT 1");
        if (! $exists) {
            try { DB::statement('ALTER TABLE normas_trechos ADD COLUMN embedding_vec vector(128)'); } catch (\Throwable $e) {}
        }

        $rows = DB::table('normas_trechos')->select('id', 'embedding')->whereNotNull('embedding')->orderBy('id')->get();
        foreach ($rows as $row) {
            $arr = is_array($row->embedding) ? $row->embedding : json_decode($row->embedding, true);
            if (! is_array($arr) || empty($arr)) { continue; }
            $vals = array_map(function($v){ return is_numeric($v) ? (string) $v : '0'; }, $arr);
            $literal = '[' . implode(',', $vals) . ']';
            try {
                DB::statement("UPDATE normas_trechos SET embedding_vec = :vec::vector WHERE id = :id", [
                    'vec' => $literal,
                    'id' => $row->id,
                ]);
            } catch (\Throwable $e) {}
        }

        $lists = intval(config('rag.ivfflat_lists', 100));
        try {
            DB::statement('CREATE INDEX IF NOT EXISTS normas_trechos_embedding_vec_ivfflat ON normas_trechos USING ivfflat (embedding_vec vector_l2_ops) WITH (lists = ' . $lists . ')');
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        try {
            $driver = DB::connection()->getDriverName();
        } catch (\Throwable $e) {
            $driver = null;
        }

        if ($driver !== 'pgsql') {
            return;
        }

        // Remove apenas a coluna de vetor; mantém extensão instalada
        $exists = DB::select("SELECT 1 FROM information_schema.columns WHERE table_name = 'normas_trechos' AND column_name = 'embedding_vec' LIMIT 1");
        if ($exists) {
            DB::statement('ALTER TABLE normas_trechos DROP COLUMN embedding_vec');
        }
    }
};
