<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'acl_main_index']);
        Permission::create(['name' => 'acl_user_index']);
        Permission::create(['name' => 'acl_user_create']);
        Permission::create(['name' => 'acl_user_edit']);
        Permission::create(['name' => 'acl_user_delete']);

        Permission::create(['name' => 'acl_role_index']);
        Permission::create(['name' => 'acl_role_show']);
        Permission::create(['name' => 'acl_role_create']);
        Permission::create(['name' => 'acl_role_edit']);
        Permission::create(['name' => 'acl_role_delete']);

        Permission::create(['name' => 'acl_log_index']);


        $writerRole = Role::create(['name' => 'user']);
        $writerRole->givePermissionTo('acl_user_index');

        $superadminRole = Role::create(['name' => 'super-admin']);
        $user = User::factory()->create([
            'name' => 'noc',
            'email' => 'noc@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $user->assignRole($writerRole);

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
        $user->assignRole($superadminRole);
    }
}
