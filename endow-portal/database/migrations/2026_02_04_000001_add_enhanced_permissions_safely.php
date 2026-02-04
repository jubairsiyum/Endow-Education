<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration safely adds new permissions and roles without disrupting
     * existing permissions that users or roles may have.
     */
    public function up(): void
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

        // Create all permissions (only if they don't exist)
        echo "Creating permissions...\n";
        $createdCount = 0;
        $existingCount = 0;
        
        foreach ($permissionsByModule as $module => $permissions) {
            foreach ($permissions as $permission) {
                $perm = Permission::firstOrCreate(['name' => $permission]);
                if ($perm->wasRecentlyCreated) {
                    $createdCount++;
                    echo "  ✓ Created: {$permission}\n";
                } else {
                    $existingCount++;
                }
            }
        }
        
        echo "Permissions Summary: {$createdCount} created, {$existingCount} already existed\n\n";

        // ===== ROLE DEFINITIONS AND ASSIGNMENTS =====
        echo "Configuring roles...\n";

        // 1. SUPER ADMIN - Complete system access
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        if ($superAdmin->wasRecentlyCreated) {
            echo "  ✓ Created role: Super Admin\n";
        }
        // Give all permissions to Super Admin (additive - won't remove existing)
        $allPermissions = Permission::all();
        $superAdmin->givePermissionTo($allPermissions);
        echo "  ✓ Super Admin now has all permissions\n";

        // 2. ADMIN - All modules except sensitive system management
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        if ($admin->wasRecentlyCreated) {
            echo "  ✓ Created role: Admin\n";
        }
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
        $this->givePermissionsIfNotExists($admin, $adminPermissions);
        echo "  ✓ Admin permissions updated\n";

        // 3. ACCOUNTANT - Only accounting module access
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        if ($accountant->wasRecentlyCreated) {
            echo "  ✓ Created role: Accountant\n";
        }
        $accountantPermissions = array_merge(
            $permissionsByModule['accounting'],
            ['view-accountant-dashboard']
        );
        $this->givePermissionsIfNotExists($accountant, $accountantPermissions);
        echo "  ✓ Accountant permissions updated\n";

        // 4. HR - Only HR and daily reports module
        $hr = Role::firstOrCreate(['name' => 'HR']);
        if ($hr->wasRecentlyCreated) {
            echo "  ✓ Created role: HR\n";
        }
        $hrPermissions = array_merge(
            $permissionsByModule['hr'],
            ['view-hr-dashboard', 'view-users']
        );
        $this->givePermissionsIfNotExists($hr, $hrPermissions);
        echo "  ✓ HR permissions updated\n";

        // 5. EMPLOYEE - Student management and follow-ups (read/write)
        $employee = Role::firstOrCreate(['name' => 'Employee']);
        if ($employee->wasRecentlyCreated) {
            echo "  ✓ Created role: Employee\n";
        }
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
        $this->givePermissionsIfNotExists($employee, $employeePermissions);
        echo "  ✓ Employee permissions updated\n";

        // 6. CONSULTANT - Student consultations and evaluations
        $consultant = Role::firstOrCreate(['name' => 'Consultant']);
        if ($consultant->wasRecentlyCreated) {
            echo "  ✓ Created role: Consultant\n";
        }
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
        $this->givePermissionsIfNotExists($consultant, $consultantPermissions);
        echo "  ✓ Consultant permissions updated\n";

        // 7. STUDENT - Own data and document management
        $student = Role::firstOrCreate(['name' => 'Student']);
        if ($student->wasRecentlyCreated) {
            echo "  ✓ Created role: Student\n";
        }
        $studentPermissions = [
            'view-student-dashboard',
            'view-documents',
            'upload-documents',
            'view-checklists',
        ];
        $this->givePermissionsIfNotExists($student, $studentPermissions);
        echo "  ✓ Student permissions updated\n";

        // Clear the cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        echo "\n✅ Enhanced permissions migration completed successfully!\n";
        echo "   All new permissions have been added without removing existing ones.\n";
    }

    /**
     * Helper method to give permissions only if they don't already exist
     */
    private function givePermissionsIfNotExists(Role $role, array $permissionNames): void
    {
        $existingPermissions = $role->permissions->pluck('name')->toArray();
        $newPermissions = array_diff($permissionNames, $existingPermissions);
        
        if (!empty($newPermissions)) {
            $role->givePermissionTo($newPermissions);
        }
    }

    /**
     * Reverse the migrations.
     * 
     * NOTE: This rollback only removes permissions that were added by this migration.
     * It will NOT remove permissions if they were assigned by other means.
     */
    public function down(): void
    {
        echo "Rolling back enhanced permissions...\n";
        
        // Define the permissions that this migration added
        $permissionsToRemove = [
            // New permissions that might not exist in old system
            'export-students',
            'approve-checklists',
            'manage-account-categories',
            'view-bank-deposits',
            'create-bank-deposits',
            'approve-bank-deposits',
            'manage-user-roles',
            'manage-user-permissions',
            'manage-programs',
            'view-analytics',
            'view-accountant-dashboard',
            'view-hr-dashboard',
            'view-departments',
            'manage-departments',
        ];

        // Remove only the new permissions (if they exist and have no assignments)
        foreach ($permissionsToRemove as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                // Check if permission is in use
                $rolesCount = $permission->roles()->count();
                $usersCount = $permission->users()->count();
                
                if ($rolesCount === 0 && $usersCount === 0) {
                    $permission->delete();
                    echo "  ✓ Removed unused permission: {$permissionName}\n";
                } else {
                    echo "  ⚠ Kept permission (in use): {$permissionName}\n";
                }
            }
        }

        // Clear the cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        echo "✅ Rollback completed\n";
    }
};
