<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:clear-cache', function () {
    $this->comment('Clearing application cache, config, routes, and views...');
    Artisan::call('cache:clear', [], $this->getOutput());
    Artisan::call('config:clear', [], $this->getOutput());
    Artisan::call('route:clear', [], $this->getOutput());
    Artisan::call('view:clear', [], $this->getOutput());
    $this->info('Cache cleared successfully.');
})->purpose('Clear cache, config, route, and view caches');

Artisan::command('app:optimize-clear', function () {
    $this->comment('Clearing cached bootstrap files (optimize:clear)...');
    Artisan::call('optimize:clear', [], $this->getOutput());
    $this->info('Optimization files cleared successfully.');
})->purpose('Clear all cached bootstrap files');

Artisan::command('app:migrate {--force : Force the operation to run when in production}', function () {
    $force = (bool) ($this->option('force') || app()->isProduction());
    $this->comment('Running database migrations...');
    Artisan::call('migrate', ['--force' => $force], $this->getOutput());
    $this->info('Migrations executed.');
})->purpose('Run database migrations with automatic production safety');
