<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
            return $this->renderUnauthorized($request, 'Maintenance Dashboard');
        }

        $tokenParam = $this->getSecretQueryParam($request);

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
     * Lock the maintenance session.
     */
    public function lock(Request $request): Response|RedirectResponse|JsonResponse
    {
        if ($request->hasSession()) {
            $request->session()->forget('maintenance_authenticated');
        }

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'success',
                'message' => 'Maintenance session locked.',
            ]);
        }

        return redirect('/maintenance');
    }

    /**
     * Run database migrations.
     */
    public function migrate(Request $request): Response|JsonResponse
    {
        if (! $this->isAuthorized($request)) {
            return $this->renderUnauthorized($request, 'Database Migration');
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
            return $this->renderUnauthorized($request, 'Migration Status');
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
            return $this->renderUnauthorized($request, 'Optimize Clear');
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
            return $this->renderUnauthorized($request, 'Cache & Optimization Clear');
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
            return $this->renderUnauthorized($request, 'Clear Cache, Optimize Clear & Run Migrate');
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
     * Retrieve the configured secret code.
     */
    protected function getExpectedSecret(): string
    {
        $secret = config('app.maintenance_secret') ?? env('MAINTENANCE_SECRET') ?? env('MAINTENANCE_TOKEN', 'shopnear@secret2026');

        return trim((string) $secret);
    }

    /**
     * Check if request has authorization secret code or active session.
     */
    protected function isAuthorized(Request $request): bool
    {
        $expectedSecret = $this->getExpectedSecret();

        if ($request->hasSession() && $request->session()->get('maintenance_authenticated') === true) {
            return true;
        }

        $providedSecret = $request->input('secret')
            ?? $request->input('code')
            ?? $request->input('token')
            ?? $request->query('secret')
            ?? $request->query('code')
            ?? $request->query('token')
            ?? $request->header('X-Maintenance-Secret')
            ?? $request->header('X-Maintenance-Token');

        if ($providedSecret !== null && hash_equals($expectedSecret, (string) $providedSecret)) {
            if ($request->hasSession()) {
                $request->session()->put('maintenance_authenticated', true);
            }

            return true;
        }

        return false;
    }

    /**
     * Get query parameter string for secret if present.
     */
    protected function getSecretQueryParam(Request $request): string
    {
        $secret = $request->query('secret') ?? $request->query('code') ?? $request->query('token');

        return ! empty($secret) ? '?secret='.urlencode((string) $secret) : '';
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
     * Render 401/403 unauthorized response or passcode form.
     */
    protected function renderUnauthorized(Request $request, string $actionName): Response|JsonResponse
    {
        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing secret code. Pass ?secret=YOUR_SECRET_CODE',
            ], 403);
        }

        $hasAttempted = $request->has('secret') || $request->has('code') || $request->has('token');
        $errorMessage = $hasAttempted ? '<div class="alert-error">Incorrect secret code. Access denied.</div>' : '';
        $actionEscaped = htmlspecialchars($actionName, ENT_QUOTES, 'UTF-8');
        $targetUrl = htmlspecialchars($request->fullUrl(), ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Required | {$actionEscaped}</title>
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --muted: #94a3b8; --border: #334155; --accent: #38bdf8; --error: #ef4444; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--text); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 1rem; }
        .card { background: var(--card); padding: 2.25rem; border-radius: 0.75rem; border: 1px solid var(--border); max-width: 440px; width: 100%; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.4); text-align: center; }
        .icon { width: 52px; height: 52px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: var(--accent); }
        h1 { margin: 0 0 0.5rem 0; font-size: 1.35rem; color: #fff; }
        p { color: var(--muted); font-size: 0.9rem; margin: 0 0 1.5rem 0; line-height: 1.5; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 0.65rem 0.9rem; border-radius: 0.5rem; font-size: 0.85rem; margin-bottom: 1.25rem; text-align: left; }
        .form-group { text-align: left; margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.04em; }
        input[type="password"], input[type="text"] { width: 100%; padding: 0.75rem 0.9rem; border-radius: 0.5rem; background: #0b1120; border: 1px solid var(--border); color: #fff; font-size: 0.95rem; outline: none; transition: border-color 0.2s; }
        input[type="password"]:focus, input[type="text"]:focus { border-color: var(--accent); }
        .btn { width: 100%; display: inline-flex; align-items: center; justify-content: center; background: #0284c7; color: #fff; border: none; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #0369a1; }
        .hint { font-size: 0.78rem; color: #64748b; margin-top: 1rem; }
        .hint code { color: #94a3b8; background: #0b1120; padding: 0.15rem 0.35rem; border-radius: 0.25rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>
        <h1>Authentication Required</h1>
        <p>Enter the custom secret code to access <strong>{$actionEscaped}</strong>.</p>
        {$errorMessage}
        <form method="POST" action="{$targetUrl}">
            <div class="form-group">
                <label for="secret">Secret Code</label>
                <input type="password" id="secret" name="secret" placeholder="Enter custom secret code..." required autofocus>
            </div>
            <button type="submit" class="btn">Unlock &amp; Execute</button>
        </form>
        <div class="hint">
            You can also pass it in the URL: <code>?secret=YOUR_CODE</code>
        </div>
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

        $secretParam = $this->getSecretQueryParam($request);
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
        .btn-danger { background: #b91c1c; }
        .btn-danger:hover { background: #991b1b; }
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
                <a href="/migrate{$secretParam}" class="btn btn-primary">Run Migrate</a>
                <a href="/optimize-clear{$secretParam}" class="btn">Optimize Clear</a>
                <a href="/clear-cache{$secretParam}" class="btn">Clear Cache</a>
                <a href="/clear-optimize-migrate{$secretParam}" class="btn btn-success">Clear &amp; Migrate All</a>
                <a href="/migrate-status{$secretParam}" class="btn">Migration Status</a>
                <a href="/maintenance{$secretParam}" class="btn">Control Center</a>
                <a href="/maintenance/lock" class="btn btn-danger">Lock Session</a>
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
        :root { --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --muted: #94a3b8; --border: #334155; --accent: #38bdf8; --success: #22c55e; --danger: #ef4444; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 2rem 1rem; }
        .container { max-width: 860px; margin: 0 auto; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 0.75rem; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
        h1 { margin: 0; font-size: 1.75rem; color: #fff; }
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
        .btn-danger { background: #b91c1c; }
        .btn-danger:hover { background: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="top-bar">
                <h1>Web Maintenance Center</h1>
                <a href="/maintenance/lock" class="btn btn-danger">Lock Session</a>
            </div>
            <p>Execute Laravel maintenance commands directly from your browser. Protected with custom secret code verification.</p>

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
