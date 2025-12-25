<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Student Management
            'view students',
            'create students',
            'edit students',
            'delete students',
            'assign students',
            'approve students',
            
            // Follow-up Management
            'view follow-ups',
            'create follow-ups',
            'edit follow-ups',
            'delete follow-ups',
            
            // Checklist Management
            'view checklists',
            'create checklists',
            'edit checklists',
            'delete checklists',
            
            // Document Management
            'view documents',
            'upload documents',
            'approve documents',
            'reject documents',
            'delete documents',
            
            // Dashboard Access
            'view admin dashboard',
            'view employee dashboard',
            'view student dashboard',
            
            // Reports
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        
        // Super Admin - Full Access
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - All except managing super admins
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view students',
            'create students',
            'edit students',
            'delete students',
            'assign students',
            'approve students',
            'view follow-ups',
            'create follow-ups',
            'edit follow-ups',
            'delete follow-ups',
            'view checklists',
            'create checklists',
            'edit checklists',
            'delete checklists',
            'view documents',
            'upload documents',
            'approve documents',
            'reject documents',
            'delete documents',
            'view admin dashboard',
            'view reports',
            'export reports',
        ]);

        // Employee - Student management, follow-ups, documents
        $employee = Role::create(['name' => 'Employee']);
        $employee->givePermissionTo([
            'view students',
            'create students',
            'edit students',
            'approve students',
            'view follow-ups',
            'create follow-ups',
            'edit follow-ups',
            'delete follow-ups',
            'view checklists',
            'view documents',
            'upload documents',
            'approve documents',
            'reject documents',
            'view employee dashboard',
        ]);

        // Student - View own dashboard and manage own documents
        $student = Role::create(['name' => 'Student']);
        $student->givePermissionTo([
            'view student dashboard',
            'view documents',
            'upload documents',
        ]);

        // Create Default Super Admin User
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@endowglobal.com',
            'phone' => '+1234567890',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $superAdminUser->assignRole('Super Admin');

        // Create Default Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@endowglobal.com',
            'phone' => '+1234567891',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('Admin');

        // Create Default Employee User
        $employeeUser = User::create([
            'name' => 'Employee User',
            'email' => 'employee@endowglobal.com',
            'phone' => '+1234567892',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $employeeUser->assignRole('Employee');

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->info('Super Admin: superadmin@endowglobal.com / password');
        $this->command->info('Admin: admin@endowglobal.com / password');
        $this->command->info('Employee: employee@endowglobal.com / password');
    }
}
