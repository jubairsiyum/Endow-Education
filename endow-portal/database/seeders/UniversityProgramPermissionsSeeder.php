<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UniversityProgramPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for universities
        $universityPermissions = [
            'view universities',
            'create universities',
            'update universities',
            'delete universities',
        ];

        foreach ($universityPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create permissions for programs
        $programPermissions = [
            'view programs',
            'create programs',
            'update programs',
            'delete programs',
        ];

        foreach ($programPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Super Admin role
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $allPermissions = array_merge($universityPermissions, $programPermissions);
            $superAdmin->givePermissionTo($allPermissions);
            echo "✓ All permissions assigned to Super Admin\n";
        }

        // Assign view and create permissions to Admin role
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = [
                'view universities',
                'create universities',
                'view programs',
                'create programs',
                'update programs',
            ];
            $admin->givePermissionTo($adminPermissions);
            echo "✓ Permissions assigned to Admin\n";
        }

        echo "✓ University and Program permissions created successfully!\n";
    }
}
