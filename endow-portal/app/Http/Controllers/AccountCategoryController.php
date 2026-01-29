<?php

namespace App\Http\Controllers;

use App\Models\AccountCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AccountCategoryController extends Controller
{
    /**
     * Display a listing of account categories.
     */
    public function index()
    {
        try {
            // Check if table exists
            if (!\Schema::hasTable('account_categories')) {
                return back()->with('error', 'Account categories table not found. Please run migrations: php artisan migrate');
            }

            $categories = AccountCategory::orderBy('type')->orderBy('name')->get();
            
            $stats = [
                'total' => AccountCategory::count(),
                'active' => AccountCategory::where('is_active', true)->count(),
                'inactive' => AccountCategory::where('is_active', false)->count(),
                'income' => AccountCategory::where('type', 'income')->count(),
                'expense' => AccountCategory::where('type', 'expense')->count(),
            ];

            return view('accounting.categories.index', compact('categories', 'stats'));
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Account Categories Index Error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), "Table") && str_contains($e->getMessage(), "doesn't exist")) {
                return back()->with('error', 'Account categories table missing. Run: php artisan migrate');
            }
            
            return back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Account Categories Index Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading categories: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('accounting.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:account_categories,name',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['is_active'] = $request->has('is_active');

            AccountCategory::create($validated);

            DB::commit();

            // Clear categories cache
            Cache::forget('active_account_categories');

            return redirect()
                ->route('office.accounting.categories.index')
                ->with('success', 'Category created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(AccountCategory $category)
    {
        return view('accounting.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, AccountCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:account_categories,name,' . $category->id,
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['is_active'] = $request->has('is_active');

            $category->update($validated);

            DB::commit();

            // Clear categories cache
            Cache::forget('active_account_categories');

            return redirect()
                ->route('office.accounting.categories.index')
                ->with('success', 'Category updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(AccountCategory $category)
    {
        // Check if category is being used
        if ($category->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete category that has transactions. Please deactivate it instead.');
        }

        try {
            $category->delete();

            // Clear categories cache
            Cache::forget('active_account_categories');

            return redirect()
                ->route('office.accounting.categories.index')
                ->with('success', 'Category deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(AccountCategory $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);

            $status = $category->is_active ? 'activated' : 'deactivated';

            // Clear categories cache
            Cache::forget('active_account_categories');

            return back()->with('success', "Category {$status} successfully!");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update category status: ' . $e->getMessage());
        }
    }
}
