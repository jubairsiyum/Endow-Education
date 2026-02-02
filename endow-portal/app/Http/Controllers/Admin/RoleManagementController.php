<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RoleManagementService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

/**
 * Role Management Controller
 *
 * Handles role and permission management for Super Admin
 */
class RoleManagementController extends Controller
{
    protected $roleService;

    public function __construct(RoleManagementService $roleService)
    {
        $this->roleService = $roleService;

        // Only Super Admin can access
        $this->middleware(['auth', 'role:Super Admin']);
    }

    /**
     * Display role management dashboard
     */
    public function index()
    {
        $roles = $this->roleService->getAllRoles();
        $statistics = $this->roleService->getStatistics();
        $protectedRoles = config('modules.protected_roles', []);

        return view('admin.roles.index', compact('roles', 'statistics', 'protectedRoles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $modules = $this->roleService->getGroupedPermissions();

        return view('admin.roles.create', compact('modules'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            // Convert permission IDs to names
            if (!empty($validated['permissions'])) {
                $validated['permissions'] = Permission::whereIn('id', $validated['permissions'])
                    ->pluck('name')
                    ->toArray();
            }

            $role = $this->roleService->createRole($validated);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Role '{$role->name}' created successfully!");
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        $modules = $this->roleService->getGroupedPermissions();
        $protectedRoles = config('modules.protected_roles', []);

        return view('admin.roles.show', compact('role', 'modules', 'protectedRoles'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $role->load('permissions', 'users');
        $modules = $this->roleService->getGroupedPermissions();
        $protectedRoles = config('modules.protected_roles', []);

        return view('admin.roles.edit', compact('role', 'modules', 'protectedRoles'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            // Convert permission IDs to names
            if (isset($validated['permissions'])) {
                $validated['permissions'] = Permission::whereIn('id', $validated['permissions'])
                    ->pluck('name')
                    ->toArray();
            } else {
                $validated['permissions'] = [];
            }

            $this->roleService->updateRole($role, $validated);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Role '{$role->name}' updated successfully!");
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        try {
            $roleName = $role->name;
            $this->roleService->deleteRole($role);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', "Role '{$roleName}' deleted successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Clone an existing role
     */
    public function clone(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        try {
            $newRole = $this->roleService->cloneRole($role, $validated['name']);

            return redirect()
                ->route('admin.roles.edit', $newRole)
                ->with('success', "Role cloned successfully as '{$newRole->name}'!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to clone role: ' . $e->getMessage());
        }
    }

    /**
     * Sync permissions from config file
     */
    public function syncPermissions()
    {
        try {
            $result = $this->roleService->syncPermissionsFromConfig();

            $message = "Permissions synced! Created: {$result['created']}, Existing: {$result['existing']}, Total: {$result['total']}";

            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to sync permissions: ' . $e->getMessage());
        }
    }

    /**
     * Show user permission assignment page
     */
    public function userPermissions(User $user)
    {
        $user->load(['roles', 'permissions']);
        $modules = $this->roleService->getGroupedPermissions();

        // Get direct permissions (not from roles)
        $directPermissions = $user->permissions;

        // Get permissions from roles
        $rolePermissions = collect();
        foreach ($user->roles as $role) {
            $rolePermissions = $rolePermissions->merge($role->permissions);
        }
        $rolePermissions = $rolePermissions->unique('id');

        return view('admin.roles.user-permissions', compact(
            'user',
            'modules',
            'directPermissions',
            'rolePermissions'
        ));
    }

    /**
     * Update user's direct permissions
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            // Convert permission IDs to names
            $permissions = [];
            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])
                    ->pluck('name')
                    ->toArray();
            }

            $this->roleService->assignPermissionsToUser($user, $permissions);

            return redirect()
                ->route('users.show', $user)
                ->with('success', "Permissions updated for user '{$user->name}'!");
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }
}
