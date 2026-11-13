<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private Role $superAdmin;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->superAdmin = Role::findByName('super-admin');
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->syncPermissions(['acl_role_index', 'acl_role_show', 'acl_role_edit', 'acl_role_delete', 'acl_user_edit']);
        $this->manager = User::factory()->create();
        $this->manager->assignRole($managerRole);
    }

    public function test_non_super_admin_cannot_see_or_touch_super_admin_role(): void
    {
        $this->actingAs($this->manager)->get(route('role.index'))->assertOk()->assertDontSee('super-admin');
        $this->actingAs($this->manager)->get(route('role.edit', $this->superAdmin))->assertForbidden();
        $this->actingAs($this->manager)->put(route('role.update', $this->superAdmin), ['name' => 'x'])->assertForbidden();
        $this->actingAs($this->manager)->delete(route('role.destroy', $this->superAdmin))->assertForbidden();
    }

    public function test_super_admin_role_cannot_be_deleted_or_renamed(): void
    {
        $admin = User::factory()->create()->assignRole('super-admin');

        $this->actingAs($admin)->delete(route('role.destroy', $this->superAdmin))->assertForbidden();
        $this->actingAs($admin)->put(route('role.update', $this->superAdmin), ['name' => 'renamed', 'permission' => ['acl_log_index']])
            ->assertRedirect(route('role.index'));

        $this->superAdmin->refresh();
        $this->assertSame('super-admin', $this->superAdmin->name);
        $this->assertTrue($this->superAdmin->hasPermissionTo('acl_log_index'));
    }

    public function test_role_update_rejects_duplicate_name_and_unknown_permission(): void
    {
        $role = Role::create(['name' => 'operator']);

        $this->actingAs($this->manager)->put(route('role.update', $role), ['name' => 'manager', 'permission' => ['nope']])
            ->assertSessionHasErrors(['name', 'permission.0']);
    }

    public function test_non_super_admin_cannot_edit_super_admin_account_or_grant_super_admin(): void
    {
        $admin = User::factory()->create()->assignRole('super-admin');
        $staff = User::factory()->create();

        $this->actingAs($this->manager)->put(route('user.update', $admin), [
            'name' => 'x', 'email' => $admin->email, 'role' => $this->superAdmin->id, 'password' => 'takeover-password',
        ])->assertForbidden();
        $this->actingAs($this->manager)->put(route('user.update', $staff), [
            'name' => 'x', 'email' => $staff->email, 'role' => $this->superAdmin->id,
        ])->assertForbidden();

        $this->assertFalse($staff->fresh()->hasRole('super-admin'));
    }

    public function test_crud_log_records_changes_without_secrets(): void
    {
        $admin = User::factory()->create()->assignRole('super-admin');
        $this->actingAs($admin)->withHeader('Authorization', 'Bearer leak-me')
            ->put(route('user.update', $this->manager), [
                'name' => 'Renamed', 'email' => $this->manager->email, 'role' => Role::findByName('manager')->id, 'password' => 'new-password-1',
            ])->assertRedirect();

        $log = \App\Models\ActivityLog::where('model', User::class)->where('event', 'updated')->latest('id')->firstOrFail();
        $this->assertArrayNotHasKey('password', $log->new_data);
        $this->assertArrayNotHasKey('authorization', $log->headers);

        $this->actingAs($admin)->get(route('audit.show', $log))->assertOk()->assertSee('Renamed')->assertDontSee('leak-me');
        $this->actingAs($this->manager)->get(route('audit.index'))->assertForbidden();
    }

    public function test_installer_creates_first_super_admin(): void
    {
        $this->artisan('app:install')
            ->expectsQuestion('Nama admin', 'Admin')
            ->expectsQuestion('Email admin', 'admin@example.test')
            ->expectsQuestion('Password admin (minimal 10 karakter)', 'correct-horse-battery')
            ->expectsQuestion('Ulangi password admin', 'correct-horse-battery')
            ->assertSuccessful();

        $this->assertTrue(User::where('email', 'admin@example.test')->firstOrFail()->hasRole('super-admin'));
    }
}
