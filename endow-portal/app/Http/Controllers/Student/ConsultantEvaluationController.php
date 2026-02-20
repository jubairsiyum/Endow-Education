<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ConsultantEvaluation;
use App\Models\EvaluationQuestion;
use App\Models\Student;
use App\Notifications\ConsultantEvaluationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsultantEvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the evaluation form for students.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is a student
        if (!$user->hasRole('Student')) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        // Check if student's application status is approved
        if ($student->status !== 'approved') {
            return view('student.consultant-evaluation.index', [
                'isApproved' => false,
                'hasConsultant' => false,
                'consultant' => null,
                'questions' => collect([]),
                'existingEvaluations' => collect([]),
            ]);
        }

        // Check if student has an assigned consultant
        if (!$student->assigned_to) {
            return view('student.consultant-evaluation.index', [
                'isApproved' => true,
                'hasConsultant' => false,
                'consultant' => null,
                'questions' => collect([]),
                'existingEvaluations' => collect([]),
            ]);
        }

        $consultant = $student->assignedUser;
        $questions = EvaluationQuestion::active()->ordered()->get();

        // Get existing evaluations
        $existingEvaluations = ConsultantEvaluation::where('student_id', $student->id)
            ->where('consultant_id', $student->assigned_to)
            ->get()
            ->keyBy('question_id');

        return view('student.consultant-evaluation.index', compact(
            'consultant',
            'questions',
            'existingEvaluations'
        ))->with('hasConsultant', true)->with('isApproved', true);
    }

    /**
     * Store or update student evaluation.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('Student')) {
            abort(403, 'Unauthorized access.');
        }

        $student = $user->student;

        if (!$student || !$student->assigned_to) {
            return redirect()->back()
                ->with('error', 'You must have an assigned consultant to submit an evaluation.');
        }

        // Check if student's application status is approved
        if ($student->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Your application must be approved before you can submit an evaluation.');
        }

        $validated = $request->validate([
            'evaluations' => 'required|array',
            'evaluations.*.question_id' => 'required|exists:evaluation_questions,id',
            'evaluations.*.rating' => 'required|in:below_average,average,neutral,good,excellent',
            'evaluations.*.comment' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['evaluations'] as $evaluation) {
                ConsultantEvaluation::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'consultant_id' => $student->assigned_to,
                        'question_id' => $evaluation['question_id'],
                    ],
                    [
                        'rating' => $evaluation['rating'],
                        'comment' => $evaluation['comment'] ?? null,
                    ]
                );
            }

            // Log activity to standard log
            Log::info('Consultant Evaluation Submitted', [
                'student_id' => $student->id,
                'student_name' => $student->full_name ?? $user->name,
                'consultant_id' => $student->assigned_to,
                'questions_evaluated' => count($validated['evaluations']),
                'user_id' => $user->id,
            ]);

            // Send notification to admins and the consultant
            try {
                $consultant = $student->assignedUser;
                
                // Only send notifications if consultant is assigned
                if ($consultant) {
                    $admins = \App\Models\User::role(['Super Admin', 'Admin'])->get();
                    
                    foreach ($admins as $admin) {
                        $admin->notify(new ConsultantEvaluationSubmittedNotification($student, $consultant));
                    }
                    
                    // Notify the consultant too
                    $consultant->notify(new ConsultantEvaluationSubmittedNotification($student, $consultant));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send consultant evaluation notification: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('student.consultant-evaluation.index')
                ->with('success', 'Your evaluation has been submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Consultant Evaluation Submission Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to submit evaluation. Please try again.')
                ->withInput();
        }
    }
}
