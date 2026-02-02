<?php

namespace App\Http\Controllers\Office;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Exception;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of departments
     */
    public function index()
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access to department management');
        }

        $departments = Department::with(['manager', 'users'])
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return view('office.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $users = User::orderBy('name')->get();
        return view('office.departments.create', compact('users'));
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:20|unique:departments',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        try {
            Department::create($validated);
            return redirect()->route('office.departments.index')
                ->with('success', 'Department created successfully!');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create department: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the department
     */
    public function edit(Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $users = User::orderBy('name')->get();
        return view('office.departments.edit', compact('department', 'users'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        try {
            $department->update($validated);
            return redirect()->route('office.departments.index')
                ->with('success', 'Department updated successfully!');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update department: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        try {
            // Check if department has users
            if ($department->users()->count() > 0) {
                return back()->with('error', 'Cannot delete department with assigned users. Please reassign them first.');
            }

            $department->delete();
            return redirect()->route('office.departments.index')
                ->with('success', 'Department deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified department
     */
    public function show(Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access');
        }

        $department->load(['manager', 'users', 'dailyReports']);
        $users = User::orderBy('name')->get();

        return view('office.departments.show', compact('department', 'users'));
    }

    /**
     * Assign user to department
     */
    public function assignUser(Request $request, Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);
            $user->department_id = $department->id;
            $user->save();

            return back()->with('success', "User {$user->name} assigned to {$department->name} successfully!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to assign user: ' . $e->getMessage());
        }
    }

    /**
     * Remove user from department
     */
    public function removeUser(Request $request, Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);
            $user->department_id = null;
            $user->save();

            return back()->with('success', "User {$user->name} removed from {$department->name}!");
        } catch (Exception $e) {
            return back()->with('error', 'Failed to remove user: ' . $e->getMessage());
        }
    }

    /**
     * Update department manager
     */
    public function updateManager(Request $request, Department $department)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'manager_id' => 'nullable|exists:users,id',
        ]);

        try {
            $department->manager_id = $validated['manager_id'];
            $department->save();

            return back()->with('success', 'Department manager updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update manager: ' . $e->getMessage());
        }
    }
}

