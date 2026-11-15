<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    /**
     * Test the maintenance index dashboard route.
     */
    public function test_maintenance_index_returns_success(): void
    {
        $response = $this->get('/maintenance');

        $response->assertStatus(200);
        $response->assertSee('Web Maintenance Center');
        $response->assertSee('/migrate');
        $response->assertSee('/optimize-clear');
        $response->assertSee('/clear-cache');
    }

    /**
     * Test the migrate web route.
     */
    public function test_migrate_route_runs_and_returns_success(): void
    {
        $response = $this->get('/migrate');

        $response->assertStatus(200);
        $response->assertSee('Database Migration');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test the migrate status web route.
     */
    public function test_migrate_status_route_returns_success(): void
    {
        $response = $this->get('/migrate-status');

        $response->assertStatus(200);
        $response->assertSee('Migration Status');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test the optimize clear web route.
     */
    public function test_optimize_clear_route_returns_success(): void
    {
        $response = $this->get('/optimize-clear');

        $response->assertStatus(200);
        $response->assertSee('Optimize Clear');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test the clear cache web route.
     */
    public function test_clear_cache_route_returns_success(): void
    {
        $response = $this->get('/clear-cache');

        $response->assertStatus(200);
        $response->assertSee('Cache & Optimization Clear');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test the clear optimize migrate combined web route.
     */
    public function test_clear_optimize_migrate_route_returns_success(): void
    {
        $response = $this->get('/clear-optimize-migrate');

        $response->assertStatus(200);
        $response->assertSee('Clear Cache, Optimize Clear & Run Migrate');
        $response->assertSee('SUCCESS');
    }

    /**
     * Test JSON format output support for API/curl consumers.
     */
    public function test_routes_support_json_format(): void
    {
        $response = $this->getJson('/migrate');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'title' => 'Database Migration',
        ]);
        $response->assertJsonStructure(['status', 'title', 'command', 'output']);
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
