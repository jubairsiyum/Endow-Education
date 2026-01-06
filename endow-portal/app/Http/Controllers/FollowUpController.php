<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    /**
     * Store a newly created follow-up in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'note' => 'required|string',
            'next_follow_up_date' => 'nullable|date|after_or_equal:today',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $this->authorize('update', $student);

        $validated['created_by'] = Auth::id();

        FollowUp::create($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Follow-up note added successfully.');
    }

    /**
     * Update the specified follow-up in storage.
     */
    public function update(Request $request, FollowUp $followUp)
    {
        $this->authorize('update', $followUp->student);

        $validated = $request->validate([
            'note' => 'required|string',
            'next_follow_up_date' => 'nullable|date|after_or_equal:today',
        ]);

        $followUp->update($validated);

        return redirect()->route('students.show', $followUp->student)
            ->with('success', 'Follow-up note updated successfully.');
    }

    /**
     * Remove the specified follow-up from storage.
     */
    public function destroy(FollowUp $followUp)
    {
        $student = $followUp->student;
        $this->authorize('update', $student);

        $followUp->delete();

        return redirect()->route('students.show', $student)
            ->with('success', 'Follow-up note deleted successfully.');
    }
}
