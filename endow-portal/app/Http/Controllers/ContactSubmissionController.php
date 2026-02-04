<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ContactSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of contact submissions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build query based on role
        $query = ContactSubmission::with(['student.user', 'assignedUser', 'responder'])
            ->whereHas('student') // Only include submissions with existing students
            ->orderBy('created_at', 'desc');

        // Employees only see submissions for their assigned students
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $submissions = $query->paginate(20);

        return view('contact-submissions.index', compact('submissions'));
    }

    /**
     * Display the specified contact submission.
     */
    public function show(ContactSubmission $contactSubmission)
    {
        $user = Auth::user();

        // Check if student exists
        if (!$contactSubmission->student || !$contactSubmission->student->user) {
            return redirect()->route('contact-submissions.index')
                ->with('error', 'This contact submission has an invalid or deleted student record.');
        }

        // Check authorization
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            if ($contactSubmission->student->assigned_to !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $contactSubmission->load(['student.user', 'assignedUser', 'responder']);

        // Get available users for assignment (Admin and Employees)
        $availableUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Admin', 'Admin', 'Employee']);
        })->orderBy('name')->get();

        return view('contact-submissions.show', compact('contactSubmission', 'availableUsers'));
    }

    /**
     * Update the status of a contact submission.
     */
    public function updateStatus(Request $request, ContactSubmission $contactSubmission)
    {
        $user = Auth::user();

        // Check authorization
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            if ($contactSubmission->student->assigned_to !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'status' => 'required|in:new,in_progress,resolved,closed',
        ]);

        $contactSubmission->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Assign a contact submission to a user.
     */
    public function assign(Request $request, ContactSubmission $contactSubmission)
    {
        $user = Auth::user();

        // Only Admin and Super Admin can assign
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $contactSubmission->update([
            'assigned_to' => $request->assigned_to,
        ]);

        return back()->with('success', 'Submission assigned successfully.');
    }

    /**
     * Add admin notes to a contact submission.
     */
    public function addNotes(Request $request, ContactSubmission $contactSubmission)
    {
        $user = Auth::user();

        // Check authorization
        if ($user->hasRole('Employee') && !$user->hasRole(['Super Admin', 'Admin'])) {
            if ($contactSubmission->student->assigned_to !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        }

        $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $contactSubmission->update([
            'admin_notes' => $request->admin_notes,
            'responded_at' => now(),
            'responded_by' => $user->id,
        ]);

        return back()->with('success', 'Notes added successfully.');
    }

    /**
     * Delete a contact submission.
     */
    public function destroy(ContactSubmission $contactSubmission)
    {
        $user = Auth::user();

        // Only Admin and Super Admin can delete
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $contactSubmission->delete();

        return redirect()->route('contact-submissions.index')
            ->with('success', 'Contact submission deleted successfully.');
    }
}
