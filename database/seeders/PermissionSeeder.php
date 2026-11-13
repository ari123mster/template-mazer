<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Idempotent: safe to rerun after adding new permissions to the list.
     */
    public function run(): void
    {
        $permissions = [
            'main_acl_index',
            'acl_user_index',
            'acl_user_create',
            'acl_user_edit',
            'acl_user_delete',
            'acl_role_index',
            'acl_role_show',
            'acl_role_create',
            'acl_role_edit',
            'acl_role_delete',
            'acl_log_index',
            'acl_audit_index',
            'acl_audit_show',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
