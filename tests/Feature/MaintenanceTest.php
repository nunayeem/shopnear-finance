<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    protected string $validSecret = 'shopnear@secret2026';

    /**
     * Test routes are protected and return 403 with passcode form when unauthenticated.
     */
    public function test_maintenance_routes_are_protected_without_secret(): void
    {
        $response = $this->get('/maintenance');
        $response->assertStatus(403);
        $response->assertSee('Authentication Required');
        $response->assertSee('Secret Code');

        $migrateResponse = $this->get('/migrate');
        $migrateResponse->assertStatus(403);
        $migrateResponse->assertSee('Authentication Required');

        $optimizeResponse = $this->get('/optimize-clear');
        $optimizeResponse->assertStatus(403);

        $clearCacheResponse = $this->get('/clear-cache');
        $clearCacheResponse->assertStatus(403);

        $combinedResponse = $this->get('/clear-optimize-migrate');
        $combinedResponse->assertStatus(403);
    }

    /**
     * Test wrong secret returns 403 with error message.
     */
    public function test_wrong_secret_returns_forbidden_with_error(): void
    {
        $response = $this->get('/maintenance?secret=wrong-secret-code');
        $response->assertStatus(403);
        $response->assertSee('Incorrect secret code. Access denied.');

        $jsonResponse = $this->getJson('/migrate?secret=wrong-secret-code');
        $jsonResponse->assertStatus(403);
        $jsonResponse->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * Test valid secret unlocks the maintenance dashboard.
     */
    public function test_valid_secret_unlocks_maintenance_index(): void
    {
        $response = $this->get('/maintenance?secret='.$this->validSecret);

        $response->assertStatus(200);
        $response->assertSee('Web Maintenance Center');
        $response->assertSee('/migrate');
        $response->assertSee('/optimize-clear');
        $response->assertSee('/clear-cache');
    }

    /**
     * Test valid secret via POST body unlocks and executes migrate.
     */
    public function test_valid_secret_via_post_executes_migrate(): void
    {
        $response = $this->post('/migrate', [
            'secret' => $this->validSecret,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Database Migration');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test valid secret unlocks migrate-status route.
     */
    public function test_valid_secret_unlocks_migrate_status(): void
    {
        $response = $this->get('/migrate-status?secret='.$this->validSecret);

        $response->assertStatus(200);
        $response->assertSee('Migration Status');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test valid secret unlocks optimize-clear route.
     */
    public function test_valid_secret_unlocks_optimize_clear(): void
    {
        $response = $this->get('/optimize-clear?secret='.$this->validSecret);

        $response->assertStatus(200);
        $response->assertSee('Optimize Clear');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test valid secret unlocks clear-cache route.
     */
    public function test_valid_secret_unlocks_clear_cache(): void
    {
        $response = $this->get('/clear-cache?secret='.$this->validSecret);

        $response->assertStatus(200);
        $response->assertSee('Cache & Optimization Clear');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test valid secret unlocks clear-optimize-migrate route.
     */
    public function test_valid_secret_unlocks_clear_optimize_migrate(): void
    {
        $response = $this->get('/clear-optimize-migrate?secret='.$this->validSecret);

        $response->assertStatus(200);
        $response->assertSee('Clear Cache, Optimize Clear & Run Migrate');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test valid secret via header with JSON response format.
     */
    public function test_valid_secret_via_header_with_json_response(): void
    {
        $response = $this->withHeaders([
            'X-Maintenance-Secret' => $this->validSecret,
        ])->getJson('/migrate');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'title' => 'Database Migration',
        ]);
    }

    /**
     * Test session persistence and locking session.
     */
    public function test_session_authentication_and_session_lock(): void
    {
        // First authenticate
        $response = $this->get('/maintenance?secret='.$this->validSecret);
        $response->assertStatus(200);

        // Subsequent call without secret param is allowed due to active session
        $followUp = $this->get('/maintenance');
        $followUp->assertStatus(200);

        // Lock session
        $lockResponse = $this->get('/maintenance/lock');
        $lockResponse->assertRedirect('/maintenance');

        // Now access is denied again
        $deniedResponse = $this->get('/maintenance');
        $deniedResponse->assertStatus(403);
    }

    /**
     * Test the artisan app:clear-optimize-migrate command.
     */
    public function test_artisan_clear_optimize_migrate_command_runs_successfully(): void
    {
        $exitCode = Artisan::call('app:clear-optimize-migrate');

        $this->assertSame(0, $exitCode);
    }
}
