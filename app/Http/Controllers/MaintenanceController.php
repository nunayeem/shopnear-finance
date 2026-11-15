<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class MaintenanceController extends Controller
{
    /**
     * Display the maintenance control center.
     */
    public function index(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request);
        }

        $tokenParam = $this->getTokenQueryParam($request);

        $html = $this->renderDashboardHtml($tokenParam);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'success',
                'message' => 'Maintenance Control Center',
                'endpoints' => [
                    'migrate' => url('/migrate'.$tokenParam),
                    'migrate_status' => url('/migrate-status'.$tokenParam),
                    'optimize_clear' => url('/optimize-clear'.$tokenParam),
                    'clear_cache' => url('/clear-cache'.$tokenParam),
                    'clear_optimize_migrate' => url('/clear-optimize-migrate'.$tokenParam),
                ],
            ]);
        }

        return response($html);
    }

    /**
     * Run database migrations.
     */
    public function migrate(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request);
        }

        $this->ensureCacheDirectoryWritable();

        $output = '';
        $success = true;

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            if (empty(trim($output))) {
                $output = 'Nothing to migrate. Database is already up to date.';
            }
        } catch (Throwable $e) {
            $success = false;
            $output = $e->getMessage()."\n\n".$e->getTraceAsString();
        }

        $this->ensureCacheDirectoryWritable();

        return $this->formatResult(
            request: $request,
            title: 'Database Migration',
            command: 'php artisan migrate --force',
            output: $output,
            success: $success
        );
    }

    /**
     * Check migration status.
     */
    public function migrateStatus(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request);
        }

        $output = '';
        $success = true;

        try {
            Artisan::call('migrate:status');
            $output = Artisan::output();
        } catch (Throwable $e) {
            $success = false;
            $output = $e->getMessage()."\n\n".$e->getTraceAsString();
        }

        return $this->formatResult(
            request: $request,
            title: 'Migration Status',
            command: 'php artisan migrate:status',
            output: $output,
            success: $success
        );
    }

    /**
     * Clear cached bootstrap files using optimize:clear.
     */
    public function optimizeClear(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request);
        }

        $this->ensureCacheDirectoryWritable();

        $output = '';
        $success = true;

        try {
            Artisan::call('optimize:clear');
            $output = Artisan::output();
        } catch (Throwable $e) {
            $success = false;
            $output = $e->getMessage()."\n\n".$e->getTraceAsString();
        }

        $this->ensureCacheDirectoryWritable();

        return $this->formatResult(
            request: $request,
            title: 'Optimize Clear',
            command: 'php artisan optimize:clear',
            output: $output,
            success: $success
        );
    }

    /**
     * Clear cache, config, route, and view caches.
     */
    public function clearCache(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request);
        }

        $this->ensureCacheDirectoryWritable();

        $logs = [];
        $success = true;

        try {
            Artisan::call('cache:clear');
            $logs[] = "cache:clear:\n".trim(Artisan::output());

            Artisan::call('config:clear');
            $logs[] = "config:clear:\n".trim(Artisan::output());

            Artisan::call('route:clear');
            $logs[] = "route:clear:\n".trim(Artisan::output());

            Artisan::call('view:clear');
            $logs[] = "view:clear:\n".trim(Artisan::output());
        } catch (Throwable $e) {
            $success = false;
            $logs[] = 'Error: '.$e->getMessage();
        }

        $this->ensureCacheDirectoryWritable();

        return $this->formatResult(
            request: $request,
            title: 'Cache & Optimization Clear',
            command: 'cache:clear, config:clear, route:clear, view:clear',
            output: implode("\n\n", $logs),
            success: $success
        );
    }

    /**
     * Run cache clear, optimize:clear, and database migrations in sequence.
     */
    public function clearOptimizeMigrate(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request);
        }

        $this->ensureCacheDirectoryWritable();

        $logs = [];
        $success = true;

        try {
            Artisan::call('optimize:clear');
            $logs[] = "optimize:clear:\n".trim(Artisan::output());

            Artisan::call('cache:clear');
            $logs[] = "cache:clear:\n".trim(Artisan::output());

            Artisan::call('config:clear');
            $logs[] = "config:clear:\n".trim(Artisan::output());

            Artisan::call('route:clear');
            $logs[] = "route:clear:\n".trim(Artisan::output());

            Artisan::call('view:clear');
            $logs[] = "view:clear:\n".trim(Artisan::output());

            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output());
            $logs[] = "migrate --force:\n".($migrateOutput !== '' ? $migrateOutput : 'Nothing to migrate.');
        } catch (Throwable $e) {
            $success = false;
            $logs[] = 'Error: '.$e->getMessage();
        }

        $this->ensureCacheDirectoryWritable();

        return $this->formatResult(
            request: $request,
            title: 'Clear Cache, Optimize Clear & Run Migrate',
            command: 'optimize:clear, cache:clear, config:clear, route:clear, view:clear, migrate --force',
            output: implode("\n\n", $logs),
            success: $success
        );
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

    /**
     * Check if request has authorization token when configured.
     */
    protected function isAuthorized(Request $request): bool
    {
        $configuredToken = config('app.maintenance_token') ?? env('MAINTENANCE_TOKEN');

        if (empty($configuredToken)) {
            return true;
        }

        $providedToken = $request->query('token') ?? $request->header('X-Maintenance-Token');

        return hash_equals((string) $configuredToken, (string) $providedToken);
    }

    /**
     * Get query parameter string for token if present.
     */
    protected function getTokenQueryParam(Request $request): string
    {
        $token = $request->query('token') ?? (config('app.maintenance_token') ?? env('MAINTENANCE_TOKEN'));

        return ! empty($token) ? '?token='.urlencode((string) $token) : '';
    }

    /**
     * Render 403 unauthorized response.
     */
    protected function renderUnauthorized(Request $request): Response|JsonResponse
    {
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please provide a valid ?token= parameter.',
            ], 403);
        }

        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Unauthorized | Maintenance Access</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #1e293b; padding: 2rem; border-radius: 0.75rem; border: 1px solid #334155; max-width: 480px; width: 90%; text-align: center; }
        h1 { color: #f87171; margin-top: 0; }
        p { color: #94a3b8; line-height: 1.5; }
        code { background: #0f172a; padding: 0.2rem 0.4rem; border-radius: 0.25rem; color: #38bdf8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>403 Forbidden</h1>
        <p>A maintenance security token is required to execute web maintenance commands.</p>
        <p>Please append <code>?token=YOUR_TOKEN</code> to the URL.</p>
    </div>
</body>
</html>
HTML;

        return response($html, 403);
    }

    /**
     * Format result as JSON or HTML response.
     */
    protected function formatResult(Request $request, string $title, string $command, string $output, bool $success): Response|JsonResponse
    {
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => $success ? 'success' : 'error',
                'title' => $title,
                'command' => $command,
                'output' => $output,
            ], $success ? 200 : 500);
        }

        $tokenParam = $this->getTokenQueryParam($request);
        $escapedOutput = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $escapedCommand = htmlspecialchars($command, ENT_QUOTES, 'UTF-8');
        $badgeClass = $success ? 'badge-success' : 'badge-error';
        $badgeText = $success ? 'SUCCESS' : 'FAILED';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$escapedTitle} | Maintenance</title>
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --muted: #94a3b8; --border: #334155; --accent: #38bdf8; --success: #22c55e; --error: #ef4444; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 2rem 1rem; line-height: 1.5; }
        .container { max-width: 860px; margin: 0 auto; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem; }
        h1 { margin: 0; font-size: 1.4rem; color: #fff; }
        .badge { font-size: 0.8rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-success { background: rgba(34, 197, 94, 0.2); color: var(--success); border: 1px solid var(--success); }
        .badge-error { background: rgba(239, 68, 68, 0.2); color: var(--error); border: 1px solid var(--error); }
        .command-bar { background: #0b1120; padding: 0.6rem 1rem; border-radius: 0.5rem; font-family: monospace; font-size: 0.9rem; color: var(--accent); margin-bottom: 1rem; border: 1px solid var(--border); overflow-x: auto; }
        pre { background: #0b1120; padding: 1rem; border-radius: 0.5rem; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 0.88rem; color: #e2e8f0; overflow-x: auto; white-space: pre-wrap; word-break: break-all; border: 1px solid var(--border); max-height: 500px; }
        .nav-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; margin-top: 1.5rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; text-align: center; background: #334155; color: #fff; text-decoration: none; padding: 0.65rem 1rem; border-radius: 0.5rem; font-size: 0.88rem; font-weight: 500; transition: background 0.2s, transform 0.1s; }
        .btn:hover { background: #475569; transform: translateY(-1px); }
        .btn-primary { background: #0284c7; }
        .btn-primary:hover { background: #0369a1; }
        .btn-success { background: #16a34a; }
        .btn-success:hover { background: #15803d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>{$escapedTitle}</h1>
                <span class="badge {$badgeClass}">{$badgeText}</span>
            </div>
            <div class="command-bar">Executed: {$escapedCommand}</div>
            <pre>{$escapedOutput}</pre>
        </div>

        <div class="card">
            <h3 style="margin-top:0; margin-bottom: 0.75rem;">Quick Maintenance Actions</h3>
            <div class="nav-grid">
                <a href="/migrate{$tokenParam}" class="btn btn-primary">Run Migrate</a>
                <a href="/optimize-clear{$tokenParam}" class="btn">Optimize Clear</a>
                <a href="/clear-cache{$tokenParam}" class="btn">Clear Cache</a>
                <a href="/clear-optimize-migrate{$tokenParam}" class="btn btn-success">Clear &amp; Migrate All</a>
                <a href="/migrate-status{$tokenParam}" class="btn">Migration Status</a>
                <a href="/{$tokenParam}" class="btn">Back to App</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;

        return response($html, $success ? 200 : 500);
    }

    /**
     * Render dashboard HTML.
     */
    protected function renderDashboardHtml(string $tokenParam): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Control Center</title>
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --muted: #94a3b8; --border: #334155; --accent: #38bdf8; --success: #22c55e; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 2rem 1rem; }
        .container { max-width: 860px; margin: 0 auto; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 0.75rem; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); }
        h1 { margin-top: 0; font-size: 1.75rem; color: #fff; }
        p { color: var(--muted); line-height: 1.6; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-top: 1.5rem; }
        .action-card { background: #0b1120; border: 1px solid var(--border); border-radius: 0.5rem; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; }
        .action-card h3 { margin: 0 0 0.5rem 0; font-size: 1.1rem; color: #fff; }
        .action-card p { font-size: 0.85rem; margin: 0 0 1rem 0; }
        .btn { display: inline-flex; align-items: center; justify-content: center; text-decoration: none; padding: 0.6rem 1rem; border-radius: 0.375rem; font-size: 0.88rem; font-weight: 500; color: #fff; background: #334155; transition: background 0.2s; }
        .btn:hover { background: #475569; }
        .btn-primary { background: #0284c7; }
        .btn-primary:hover { background: #0369a1; }
        .btn-success { background: #16a34a; }
        .btn-success:hover { background: #15803d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Web Maintenance Center</h1>
            <p>Execute Laravel maintenance commands directly from your browser. Ideal for hosting environments without SSH terminal access.</p>

            <div class="grid">
                <div class="action-card">
                    <div>
                        <h3>Run Migrate</h3>
                        <p>Runs all pending migrations using <code>migrate --force</code>.</p>
                    </div>
                    <a href="/migrate{$tokenParam}" class="btn btn-primary">Run Migrate</a>
                </div>

                <div class="action-card">
                    <div>
                        <h3>Optimize Clear</h3>
                        <p>Clears compiled bootstrap cache files using <code>optimize:clear</code>.</p>
                    </div>
                    <a href="/optimize-clear{$tokenParam}" class="btn">Run Optimize Clear</a>
                </div>

                <div class="action-card">
                    <div>
                        <h3>Clear Cache</h3>
                        <p>Clears application cache, config, routes, and compiled views.</p>
                    </div>
                    <a href="/clear-cache{$tokenParam}" class="btn">Clear Cache</a>
                </div>

                <div class="action-card">
                    <div>
                        <h3>Clear &amp; Migrate All</h3>
                        <p>One-click full maintenance: clears all caches, clears optimization, and runs migrations.</p>
                    </div>
                    <a href="/clear-optimize-migrate{$tokenParam}" class="btn btn-success">Execute All</a>
                </div>

                <div class="action-card">
                    <div>
                        <h3>Migration Status</h3>
                        <p>Inspect the status and batch number of each migration.</p>
                    </div>
                    <a href="/migrate-status{$tokenParam}" class="btn">Check Status</a>
                </div>

                <div class="action-card">
                    <div>
                        <h3>Application Home</h3>
                        <p>Return to the main application landing page.</p>
                    </div>
                    <a href="/{$tokenParam}" class="btn">Go to App</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
