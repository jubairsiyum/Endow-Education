<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view universities');

        $universities = University::with('creator')
            ->withCount('programs', 'students')
            ->ordered()
            ->paginate(20);

        return view('universities.index', compact('universities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create universities');
        return view('universities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create universities');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:universities,code',
            'country' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active') ? ($request->is_active == '1' || $request->is_active === true) : true;

        if (!isset($validated['order'])) {
            $validated['order'] = University::max('order') + 1;
        }

        University::create($validated);

        return redirect()->route('universities.index')
            ->with('success', 'University created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university)
    {
        $this->authorize('view universities');

        $university->load(['programs' => function($query) {
            $query->withCount('students')->ordered();
        }, 'creator']);

        return view('universities.show', compact('university'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(University $university)
    {
        $this->authorize('update universities');
        return view('universities.edit', compact('university'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, University $university)
    {
        $this->authorize('update universities');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:universities,code,' . $university->id,
            'country' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active') ? ($request->is_active == '1' || $request->is_active === true) : false;

        $university->update($validated);

        return redirect()->route('universities.index')
            ->with('success', 'University updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university)
    {
        $this->authorize('delete universities');

        if ($university->students()->count() > 0) {
            return back()->with('error', 'Cannot delete university with assigned students.');
        }

        $university->delete();

        return redirect()->route('universities.index')
            ->with('success', 'University deleted successfully.');
    }
}
