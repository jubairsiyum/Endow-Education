<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\University;
use App\Models\ChecklistItem;
use App\Models\StudentChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view programs');

        $query = Program::with(['university', 'creator'])
            ->withCount('students', 'checklistItems');

        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $programs = $query->ordered()->paginate(20);
        $universities = University::active()->ordered()->get();

        return view('programs.index', compact('programs', 'universities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create programs');

        $universities = University::active()->ordered()->get();
        $checklistItems = ChecklistItem::active()->ordered()->get();

        return view('programs.create', compact('universities', 'checklistItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create programs');

        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:programs,code',
            'level' => 'required|in:undergraduate,postgraduate,phd,diploma,certificate',
            'duration' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tuition_fee' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:0',
            'default_deadline' => 'nullable|date',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'exists:checklist_items,id',
            'document_deadlines' => 'nullable|array',
            'document_deadlines.*.checklist_item_id' => 'required_with:document_deadlines|exists:checklist_items,id',
            'document_deadlines.*.has_specific' => 'sometimes|boolean',
            'document_deadlines.*.specific_deadline' => 'nullable|date|required_if:document_deadlines.*.has_specific,true',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active') ? ($request->is_active == '1' || $request->is_active === true) : true;

        if (!isset($validated['order'])) {
            $validated['order'] = Program::where('university_id', $validated['university_id'])->max('order') + 1;
        }

        $checklistItems = $validated['checklist_items'] ?? [];
        $documentDeadlines = $validated['document_deadlines'] ?? [];
        unset($validated['checklist_items'], $validated['document_deadlines']);

        $program = Program::create($validated);

        if (!empty($checklistItems)) {
            $program->checklistItems()->sync($checklistItems);
        }

        // Save document-specific deadlines
        if (!empty($documentDeadlines)) {
            $this->saveDocumentDeadlines($program, $documentDeadlines);
        }

        return redirect()->route('programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        $this->authorize('view programs');

        $program->load(['university', 'checklistItems', 'creator']);
        $program->loadCount('students');

        return view('programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $this->authorize('update programs');

        $universities = University::active()->ordered()->get();
        $checklistItems = ChecklistItem::active()->ordered()->get();
        $program->load(['checklistItems', 'documentDeadlines']);

        return view('programs.edit', compact('program', 'universities', 'checklistItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $this->authorize('update programs');

        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:programs,code,' . $program->id,
            'level' => 'required|in:undergraduate,postgraduate,phd,diploma,certificate',
            'duration' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tuition_fee' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'sometimes|boolean',
            'order' => 'nullable|integer|min:0',
            'default_deadline' => 'nullable|date',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'exists:checklist_items,id',
            'document_deadlines' => 'nullable|array',
            'document_deadlines.*.checklist_item_id' => 'required_with:document_deadlines|exists:checklist_items,id',
            'document_deadlines.*.has_specific' => 'sometimes|boolean',
            'document_deadlines.*.specific_deadline' => 'nullable|date|required_if:document_deadlines.*.has_specific,true',
        ]);

        $validated['is_active'] = $request->has('is_active') ? ($request->is_active == '1' || $request->is_active === true) : false;

        $checklistItems = $validated['checklist_items'] ?? [];
        $documentDeadlines = $validated['document_deadlines'] ?? [];
        unset($validated['checklist_items'], $validated['document_deadlines']);

        $program->update($validated);
        $program->checklistItems()->sync($checklistItems);

        // Clear the relationship cache to ensure fresh data on next load
        $program->load('checklistItems');

        // Update document-specific deadlines
        if (!empty($documentDeadlines)) {
            $this->saveDocumentDeadlines($program, $documentDeadlines);
        } else {
            // Clear all specific deadlines if none provided
            $program->documentDeadlines()->delete();
        }

        // Update student checklists for students enrolled in this program
        $this->updateStudentChecklistsForProgram($program);

        return redirect()->route('programs.index')
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $this->authorize('delete programs');

        if ($program->students()->count() > 0) {
            return back()->with('error', 'Cannot delete program with assigned students.');
        }

        $program->delete();

        return redirect()->route('programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Save or update document-specific deadlines for a program
     */
    protected function saveDocumentDeadlines(Program $program, array $documentDeadlines)
    {
        foreach ($documentDeadlines as $deadline) {
            $program->documentDeadlines()->updateOrCreate(
                [
                    'checklist_item_id' => $deadline['checklist_item_id'],
                ],
                [
                    'has_specific_deadline' => $deadline['has_specific'] ?? false,
                    'specific_deadline' => ($deadline['has_specific'] ?? false) ? ($deadline['specific_deadline'] ?? null) : null,
                ]
            );
        }
    }

    /**
     * Update student checklists for all students enrolled in this program
     * This ensures students see updated checklist items when program requirements change
     */
    protected function updateStudentChecklistsForProgram(Program $program)
    {
        $students = $program->students;

        foreach ($students as $student) {
            // Get the new checklist items for this program
            $newChecklistItemIds = $program->checklistItems()->pluck('checklist_items.id')->toArray();

            // Get existing student checklist items
            $existingChecklistItemIds = $student->checklists()->pluck('checklist_item_id')->toArray();

            // Add new checklist items that don't exist yet
            $itemsToAdd = array_diff($newChecklistItemIds, $existingChecklistItemIds);
            foreach ($itemsToAdd as $itemId) {
                StudentChecklist::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'checklist_item_id' => $itemId,
                    ],
                    [
                        'status' => 'pending',
                    ]
                );
            }

            // Remove checklist items that are no longer in the program (only if pending)
            $itemsToRemove = array_diff($existingChecklistItemIds, $newChecklistItemIds);
            if (!empty($itemsToRemove)) {
                StudentChecklist::where('student_id', $student->id)
                    ->whereIn('checklist_item_id', $itemsToRemove)
                    ->where('status', 'pending') // Only remove pending items
                    ->delete();
            }
        }
    }

    /**
     * Get programs by university (API endpoint for dynamic dropdowns)
     */
    public function byUniversity(Request $request, University $university)
    {
        $programs = $university->programs()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'code', 'level']);

        return response()->json($programs);
    }
}
