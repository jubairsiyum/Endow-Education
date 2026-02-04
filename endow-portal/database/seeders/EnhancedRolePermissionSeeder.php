<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EnhancedRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates a comprehensive module-based permission system
     * allowing granular control over user access to different parts of the application.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions organized by modules
        $permissionsByModule = [
            // ===== STUDENT MANAGEMENT MODULE =====
            'students' => [
                'view-students',
                'create-students',
                'edit-students',
                'delete-students',
                'assign-students',
                'approve-students',
                'export-students',
            ],

            // ===== FOLLOW-UP MANAGEMENT MODULE =====
            'follow-ups' => [
                'view-follow-ups',
                'create-follow-ups',
                'edit-follow-ups',
                'delete-follow-ups',
            ],

            // ===== CHECKLIST & DOCUMENTS MODULE =====
            'checklists' => [
                'view-checklists',
                'create-checklists',
                'edit-checklists',
                'delete-checklists',
                'approve-checklists',
            ],

            'documents' => [
                'view-documents',
                'upload-documents',
                'approve-documents',
                'reject-documents',
                'delete-documents',
            ],

            // ===== ACCOUNTING MODULE =====
            'accounting' => [
                'view-accounting',
                'view-transaction',
                'create-transaction',
                'update-transaction',
                'delete-transaction',
                'approve-transaction',
                'view-accounting-summary',
                'manage-account-categories',
                'view-bank-deposits',
                'create-bank-deposits',
                'approve-bank-deposits',
            ],

            // ===== HR & DAILY REPORTS MODULE =====
            'hr' => [
                'view-daily-reports',
                'create-daily-reports',
                'edit-daily-reports',
                'delete-daily-reports',
                'approve-daily-reports',
                'view-departments',
                'manage-departments',
            ],

            // ===== USER MANAGEMENT MODULE =====
            'users' => [
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'manage-user-roles',
                'manage-user-permissions',
            ],

            // ===== UNIVERSITY & PROGRAMS MODULE =====
            'universities' => [
                'view-universities',
                'create-universities',
                'edit-universities',
                'delete-universities',
                'manage-programs',
            ],

            // ===== REPORTS & ANALYTICS MODULE =====
            'reports' => [
                'view-reports',
                'export-reports',
                'view-analytics',
            ],

            // ===== CONSULTANT EVALUATION MODULE =====
            'evaluations' => [
                'view-evaluations',
                'create-evaluations',
                'edit-evaluations',
                'delete-evaluations',
            ],

            // ===== DASHBOARD ACCESS =====
            'dashboard' => [
                'view-admin-dashboard',
                'view-employee-dashboard',
                'view-student-dashboard',
                'view-accountant-dashboard',
                'view-hr-dashboard',
            ],
        ];

        // Create all permissions
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
        }

        // ===== ROLE DEFINITIONS =====

        // 1. SUPER ADMIN - Complete system access
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. ADMIN - All modules except sensitive system management
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $adminPermissions = array_merge(
            $permissionsByModule['students'],
            $permissionsByModule['follow-ups'],
            $permissionsByModule['checklists'],
            $permissionsByModule['documents'],
            $permissionsByModule['universities'],
            $permissionsByModule['reports'],
            $permissionsByModule['evaluations'],
            ['view-users', 'view-admin-dashboard']
        );
        $admin->syncPermissions($adminPermissions);

        // 3. ACCOUNTANT - Only accounting module access
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountantPermissions = array_merge(
            $permissionsByModule['accounting'],
            ['view-accountant-dashboard']
        );
        $accountant->syncPermissions($accountantPermissions);

        // 4. HR - Only HR and daily reports module
        $hr = Role::firstOrCreate(['name' => 'HR']);
        $hrPermissions = array_merge(
            $permissionsByModule['hr'],
            ['view-hr-dashboard', 'view-users']
        );
        $hr->syncPermissions($hrPermissions);

        // 5. EMPLOYEE - Student management and follow-ups (read/write)
        $employee = Role::firstOrCreate(['name' => 'Employee']);
        $employeePermissions = [
            'view-students',
            'create-students',
            'edit-students',
            'assign-students',
            'approve-students',
            'view-follow-ups',
            'create-follow-ups',
            'edit-follow-ups',
            'view-checklists',
            'view-documents',
            'approve-documents',
            'view-universities',
            'view-employee-dashboard',
        ];
        $employee->syncPermissions($employeePermissions);

        // 6. CONSULTANT - Student consultations and evaluations
        $consultant = Role::firstOrCreate(['name' => 'Consultant']);
        $consultantPermissions = [
            'view-students',
            'view-follow-ups',
            'create-follow-ups',
            'view-evaluations',
            'create-evaluations',
            'edit-evaluations',
            'view-universities',
            'view-employee-dashboard',
        ];
        $consultant->syncPermissions($consultantPermissions);

        // 7. STUDENT - Own data and document management
        $student = Role::firstOrCreate(['name' => 'Student']);
        $studentPermissions = [
            'view-student-dashboard',
            'view-documents',
            'upload-documents',
            'view-checklists',
        ];
        $student->syncPermissions($studentPermissions);

        $this->command->info('✅ Enhanced Role and Permission system created successfully!');
        $this->command->info('');
        $this->command->info('Available Roles:');
        $this->command->info('1. Super Admin - Full system access');
        $this->command->info('2. Admin - Student & University management');
        $this->command->info('3. Accountant - Accounting module only');
        $this->command->info('4. HR - Daily reports & HR management');
        $this->command->info('5. Employee - Student operations');
        $this->command->info('6. Consultant - Student consultation & evaluation');
        $this->command->info('7. Student - Personal dashboard & documents');
    }
}
