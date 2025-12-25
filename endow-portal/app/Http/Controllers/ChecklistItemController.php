<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:create checklists');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $checklistItems = ChecklistItem::orderBy('order')->get();
        return view('checklist-items.index', compact('checklistItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('checklist-items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $maxOrder = ChecklistItem::max('order') ?? 0;

        ChecklistItem::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
            'order' => $maxOrder + 1,
        ]);

        return redirect()
            ->route('checklist-items.index')
            ->with('success', 'Checklist item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecklistItem $checklistItem)
    {
        return view('checklist-items.edit', compact('checklistItem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChecklistItem $checklistItem)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $checklistItem->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('checklist-items.index')
            ->with('success', 'Checklist item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChecklistItem $checklistItem)
    {
        $checklistItem->delete();

        return redirect()
            ->route('checklist-items.index')
            ->with('success', 'Checklist item deleted successfully!');
    }
}
