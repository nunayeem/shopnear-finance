<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('app:clear-optimize-migrate {--fresh : Drop all tables and re-run all migrations} {--seed : Seed the database with records} {--force : Force the operation to run when in production}')]
#[Description('Clear application cache, optimize:clear, and run database migrations.')]
class ClearOptimizeMigrateCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->ensureCacheDirectoryWritable();

        $this->info('Starting cache clearing, optimization reset, and migration...');

        $this->line('');
        $this->comment('1. Running optimize:clear...');
        Artisan::call('optimize:clear', [], $this->getOutput());
        $this->ensureCacheDirectoryWritable();

        $this->line('');
        $this->comment('2. Running cache:clear...');
        Artisan::call('cache:clear', [], $this->getOutput());

        $this->line('');
        $this->comment('3. Running config:clear...');
        Artisan::call('config:clear', [], $this->getOutput());

        $this->line('');
        $this->comment('4. Running route:clear...');
        Artisan::call('route:clear', [], $this->getOutput());

        $this->line('');
        $this->comment('5. Running view:clear...');
        Artisan::call('view:clear', [], $this->getOutput());

        $force = (bool) ($this->option('force') || app()->isProduction());

        $this->line('');
        if ($this->option('fresh')) {
            $this->comment('6. Running migrate:fresh...');
            $migrationParameters = ['--force' => $force];
            if ($this->option('seed')) {
                $migrationParameters['--seed'] = true;
            }
            Artisan::call('migrate:fresh', $migrationParameters, $this->getOutput());
        } else {
            $this->comment('6. Running migrate...');
            Artisan::call('migrate', ['--force' => $force], $this->getOutput());

            if ($this->option('seed')) {
                $this->line('');
                $this->comment('7. Running db:seed...');
                Artisan::call('db:seed', ['--force' => $force], $this->getOutput());
            }
        }

        $this->ensureCacheDirectoryWritable();

        $this->line('');
        $this->info('All maintenance actions completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Ensure bootstrap/cache directory exists and is writable across platforms.
     */
    protected function ensureCacheDirectoryWritable(): void
    {
        $cacheDir = base_path('bootstrap/cache');

        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        @chmod($cacheDir, 0777);

        if (PHP_OS_FAMILY === 'Windows') {
            @exec('attrib -r "'.$cacheDir.'" /D');
        }
    }
}
