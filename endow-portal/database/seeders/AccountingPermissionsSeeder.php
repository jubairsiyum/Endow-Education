<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccountingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define accounting permissions
        $permissions = [
            'view-accounting',
            'view-transaction',
            'create-transaction',
            'update-transaction',
            'delete-transaction',
            'approve-transaction',
            'view-accounting-summary',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to Super Admin role
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Create Accountant role if it doesn't exist
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        
        // Assign all accounting permissions to Accountant
        $accountant->givePermissionTo($permissions);

        echo "Accounting permissions created and assigned successfully!\n";
    }
}
