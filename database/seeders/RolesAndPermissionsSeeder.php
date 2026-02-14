<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Clean up tables
        Schema::disableForeignKeyConstraints();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create permissions
        $permissions = [
            'view clients',
            'edit clients',
            'delete clients',
            'manage users',
            'view zapytania',
            'manage zapytania',
            'view emails',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Super Admin - no specific permissions assigned because of Gate::before
        Role::create(['name' => 'super-admin']);

        // 2. Administrator - gets all permissions
        $admin = Role::create(['name' => 'Administrator']);
        $admin->givePermissionTo(Permission::all());

        // 3. Kierownictwo
        $kierownictwo = Role::create(['name' => 'Kierownictwo']);
        $kierownictwo->givePermissionTo([
            'view clients',
            'edit clients',
            'view zapytania',
            'manage zapytania',
            'manage users',
            'view emails'
        ]);

        // 4. Eksport
        $eksport = Role::create(['name' => 'Eksport']);
        $eksport->givePermissionTo([
            'view clients',
            'view zapytania'
        ]);

        // 5. Techniczny
        $techniczny = Role::create(['name' => 'Techniczny']);
        $techniczny->givePermissionTo([
            'view clients',
            'view zapytania'
        ]);

        $this->command->info('Roles and permissions seeded successfully after cleanup!');
    }
}
