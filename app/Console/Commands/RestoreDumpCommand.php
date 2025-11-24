<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestoreDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:restore-dump
        {--dump=Dump/Dump20251029 : Caminho relativo do diretório do dump}
        {--force : Executa sem confirmação adicional}
        {--confirm-remote : Confirma execução em host não-local}
        {--dry-run : Exibe o plano de restauração sem executar}';

    /**
     * The console command description.
     */
    protected $description = 'Restaura o banco a partir de arquivos .sql no diretório Dump, com validações de segurança.';

    public function handle(): int
    {
        $dumpDirRel = $this->option('dump');
        $dumpDir = base_path($dumpDirRel);

        if (! is_dir($dumpDir)) {
            $this->error("Diretório de dump não encontrado: {$dumpDirRel}");

            return self::FAILURE;
        }

        $files = collect(File::files($dumpDir))
            ->filter(fn ($f) => str_ends_with(strtolower($f->getFilename()), '.sql'))
            ->sortBy(fn ($f) => $f->getFilename())
            ->values();

        if ($files->isEmpty()) {
            $this->warn('Nenhum arquivo .sql encontrado no diretório informado.');

            return self::FAILURE;
        }

        // Segurança: evitar execução acidental em produção/host remoto
        $connection = config('database.default');
        $connCfg = config('database.connections.' . $connection, []);
        $dbHost = $connCfg['host'] ?? env('DB_HOST');
        $dbName = $connCfg['database'] ?? env('DB_DATABASE');

        $this->line("Conexão: {$connection} | Host: {$dbHost} | Database: {$dbName}");

        $isLocalHost = in_array($dbHost, ['127.0.0.1', 'localhost']);
        $isTestingEnv = App::environment('testing');

        if ($this->option('dry-run')) {
            $this->info('Dry-run ativo. Listando arquivos que seriam importados:');
            foreach ($files as $f) {
                $this->line('- ' . $f->getFilename());
            }

            return self::SUCCESS;
        }

        if (! $this->option('force')) {
            $this->error('Por segurança, use --force para executar.');

            return self::FAILURE;
        }

        if (! $isLocalHost && ! $isTestingEnv && ! $this->option('confirm-remote')) {
            $this->error('Host não-local detectado. Confirme com --confirm-remote para prosseguir.');

            return self::FAILURE;
        }

        $this->warn('Desativando verificações de chave estrangeira...');
        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

        $erroCount = 0;
        foreach ($files as $file) {
            $name = $file->getFilename();
            $path = $file->getPathname();
            $sql = File::get($path);

            $this->info("Importando: {$name}");
            try {
                DB::unprepared($sql);
            } catch (\Throwable $e) {
                $erroCount++;
                $this->error("Falha ao importar {$name}: " . $e->getMessage());
                // Tenta fallback por statements separados por ;\n
                try {
                    $statements = collect(preg_split('/;\s*\n/', $sql))
                        ->map(fn ($s) => trim($s))
                        ->filter();
                    foreach ($statements as $stmt) {
                        if ($stmt === '') {
                            continue;
                        }
                        DB::unprepared($stmt . ';');
                    }
                    $this->warn("Importação por fallback concluída para {$name}.");
                } catch (\Throwable $e2) {
                    $this->error("Fallback também falhou em {$name}: " . $e2->getMessage());
                }
            }
        }

        $this->warn('Reativando verificações de chave estrangeira...');
        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');

        if ($erroCount > 0) {
            $this->warn("Restauração finalizada com {$erroCount} arquivo(s) com erro. Verifique os logs.");
        } else {
            $this->info('Restauração concluída com sucesso.');
        }

        return self::SUCCESS;
    }
}
