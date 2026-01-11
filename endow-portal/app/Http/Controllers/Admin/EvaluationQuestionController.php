<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluationQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    /**
     * Display a listing of evaluation questions.
     */
    public function index()
    {
        $questions = EvaluationQuestion::with('creator')
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.evaluation-questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        return view('admin.evaluation-questions.create');
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            EvaluationQuestion::create([
                'question' => $validated['question'],
                'order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.evaluation-questions.index')
                ->with('success', 'Evaluation question created successfully.');
        } catch (\Exception $e) {
            Log::error('Evaluation Question Creation Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create question. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing a question.
     */
    public function edit(EvaluationQuestion $evaluationQuestion)
    {
        return view('admin.evaluation-questions.edit', compact('evaluationQuestion'));
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, EvaluationQuestion $evaluationQuestion)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $evaluationQuestion->update([
                'question' => $validated['question'],
                'order' => $validated['order'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()->route('admin.evaluation-questions.index')
                ->with('success', 'Evaluation question updated successfully.');
        } catch (\Exception $e) {
            Log::error('Evaluation Question Update Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update question. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified question.
     */
    public function destroy(EvaluationQuestion $evaluationQuestion)
    {
        try {
            $evaluationQuestion->delete();

            return redirect()->route('admin.evaluation-questions.index')
                ->with('success', 'Evaluation question deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Evaluation Question Deletion Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete question. Please try again.');
        }
    }

    /**
     * Toggle question status (active/inactive).
     */
    public function toggleStatus(EvaluationQuestion $evaluationQuestion)
    {
        try {
            $evaluationQuestion->update([
                'is_active' => !$evaluationQuestion->is_active,
            ]);

            $status = $evaluationQuestion->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "Question {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Evaluation Question Toggle Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to toggle question status.');
        }
    }
}
