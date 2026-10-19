<?php

namespace Tests\Feature;

use App\Filament\Pages\SystemMaintenance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SystemMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_system_maintenance_page(): void
    {
        $response = $this->get('/admin/system-maintenance');

        $response->assertRedirect('/admin/login');
    }

    public function test_regular_participant_cannot_access_system_maintenance_page(): void
    {
        $participant = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($participant)->get('/admin/system-maintenance');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_system_maintenance_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/system-maintenance');

        $response->assertStatus(200);
        $response->assertSee('Pemeliharaan Sistem &amp; Database', false);
        $response->assertSee('Storage Symlink');
        $response->assertSee('Migrasi Database');
    }

    public function test_admin_can_execute_migration_status_action(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        Livewire::test(SystemMaintenance::class)
            ->call('runMigrationStatus')
            ->assertSet('lastCommand', 'php artisan migrate:status')
            ->assertSet('isSuccess', true)
            ->assertNotSet('commandOutput', null);
    }

    public function test_admin_can_execute_storage_link_action(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        Livewire::test(SystemMaintenance::class)
            ->call('runStorageLink')
            ->assertSet('lastCommand', 'php artisan storage:link')
            ->assertSet('isSuccess', true);
    }

    public function test_admin_can_execute_optimize_clear_action(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        Livewire::test(SystemMaintenance::class)
            ->call('runOptimizeClear')
            ->assertSet('lastCommand', 'php artisan optimize:clear')
            ->assertSet('isSuccess', true);
    }
}
