<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Role Management Service
 *
 * Handles business logic for role and permission management
 */
class RoleManagementService
{
    /**
     * Get all modules with permissions from config
     */
    public function getModules(): array
    {
        return config('modules.modules', []);
    }

    /**
     * Get all permissions grouped by module
     */
    public function getGroupedPermissions(): array
    {
        $modules = $this->getModules();
        $grouped = [];

        foreach ($modules as $key => $module) {
            $grouped[$key] = [
                'name' => $module['name'],
                'icon' => $module['icon'],
                'description' => $module['description'],
                'permissions' => [],
            ];

            foreach ($module['permissions'] as $permissionKey => $permissionName) {
                $permission = Permission::where('name', $permissionKey)->first();
                if ($permission) {
                    $grouped[$key]['permissions'][] = [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => $permissionName,
                    ];
                }
            }
        }

        return $grouped;
    }

    /**
     * Get all roles with permission count
     */
    public function getAllRoles()
    {
        return Role::withCount('permissions')->get();
    }

    /**
     * Create a new role with permissions
     */
    public function createRole(array $data): Role
    {
        try {
            DB::beginTransaction();

            // Create role
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            // Assign permissions if provided
            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            // Clear permission cache
            $this->clearPermissionCache();

            Log::info('Role created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => count($data['permissions'] ?? []),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create role', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing role
     */
    public function updateRole(Role $role, array $data): Role
    {
        try {
            DB::beginTransaction();

            // Check if role is protected
            if ($this->isProtectedRole($role->name)) {
                throw new Exception('Cannot modify protected role: ' . $role->name);
            }

            // Update role name if changed
            if (!empty($data['name']) && $data['name'] !== $role->name) {
                $role->update(['name' => $data['name']]);
            }

            // Sync permissions
            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            // Clear permission cache
            $this->clearPermissionCache();

            Log::info('Role updated', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => $role->permissions()->count(),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return $role->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update role', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a role
     */
    public function deleteRole(Role $role): bool
    {
        try {
            DB::beginTransaction();

            // Check if role is protected
            if ($this->isProtectedRole($role->name)) {
                throw new Exception('Cannot delete protected role: ' . $role->name);
            }

            // Check if role has users
            if ($role->users()->count() > 0) {
                throw new Exception('Cannot delete role with assigned users. Please reassign users first.');
            }

            $roleId = $role->id;
            $roleName = $role->name;

            $role->delete();

            // Clear permission cache
            $this->clearPermissionCache();

            Log::info('Role deleted', [
                'role_id' => $roleId,
                'role_name' => $roleName,
                'deleted_by' => auth()->id(),
            ]);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete role', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Assign specific permissions to a user
     */
    public function assignPermissionsToUser(User $user, array $permissions): User
    {
        try {
            DB::beginTransaction();

            // Sync direct permissions
            $user->syncPermissions($permissions);

            // Clear permission cache
            $this->clearPermissionCache();

            Log::info('User permissions updated', [
                'user_id' => $user->id,
                'permissions_count' => count($permissions),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return $user->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign permissions to user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Sync all permissions from modules config
     */
    public function syncPermissionsFromConfig(): array
    {
        try {
            DB::beginTransaction();

            $modules = $this->getModules();
            $systemPermissions = config('modules.system_permissions', []);
            $createdCount = 0;
            $existingCount = 0;

            // Process module permissions
            foreach ($modules as $module) {
                foreach ($module['permissions'] as $permissionKey => $permissionName) {
                    $permission = Permission::firstOrCreate(
                        ['name' => $permissionKey],
                        ['guard_name' => 'web']
                    );

                    if ($permission->wasRecentlyCreated) {
                        $createdCount++;
                    } else {
                        $existingCount++;
                    }
                }
            }

            // Process system permissions
            foreach ($systemPermissions as $permissionKey => $permissionName) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionKey],
                    ['guard_name' => 'web']
                );

                if ($permission->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $existingCount++;
                }
            }

            // Clear permission cache
            $this->clearPermissionCache();

            Log::info('Permissions synced from config', [
                'created' => $createdCount,
                'existing' => $existingCount,
                'synced_by' => auth()->id(),
            ]);

            DB::commit();

            return [
                'created' => $createdCount,
                'existing' => $existingCount,
                'total' => $createdCount + $existingCount,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync permissions', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get statistics for role management dashboard
     */
    public function getStatistics(): array
    {
        return [
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_users_with_roles' => User::role(Role::pluck('name')->toArray())->count(),
            'protected_roles' => count(config('modules.protected_roles', [])),
        ];
    }

    /**
     * Check if a role is protected
     */
    public function isProtectedRole(string $roleName): bool
    {
        $protectedRoles = config('modules.protected_roles', []);
        return in_array($roleName, $protectedRoles);
    }

    /**
     * Get user's direct permissions (not from roles)
     */
    public function getUserDirectPermissions(User $user)
    {
        return $user->permissions;
    }

    /**
     * Get all user permissions (from roles and direct)
     */
    public function getAllUserPermissions(User $user)
    {
        return $user->getAllPermissions();
    }

    /**
     * Clear permission cache
     */
    protected function clearPermissionCache(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');
    }

    /**
     * Clone a role with its permissions
     */
    public function cloneRole(Role $sourceRole, string $newRoleName): Role
    {
        try {
            DB::beginTransaction();

            // Create new role
            $newRole = Role::create([
                'name' => $newRoleName,
                'guard_name' => $sourceRole->guard_name,
            ]);

            // Copy permissions
            $permissions = $sourceRole->permissions->pluck('name')->toArray();
            $newRole->givePermissionTo($permissions);

            // Clear permission cache
            $this->clearPermissionCache();

            Log::info('Role cloned', [
                'source_role' => $sourceRole->name,
                'new_role' => $newRoleName,
                'permissions_copied' => count($permissions),
                'cloned_by' => auth()->id(),
            ]);

            DB::commit();

            return $newRole;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to clone role', [
                'source_role' => $sourceRole->name,
                'new_role_name' => $newRoleName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
