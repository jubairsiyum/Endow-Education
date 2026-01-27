<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\ContactSubmissionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentChecklistController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\StudentVisitController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\StudentRegisterController;
use App\Http\Controllers\Auth\StudentPasswordResetController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Office\DailyReportController;
use App\Http\Controllers\Office\DepartmentController;
use App\Http\Controllers\Admin\RoleManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Storage file serving route (for shared hosting without symlink)
Route::get('/storage/{path}', function ($path) {
    // Security: Prevent directory traversal
    $path = str_replace(['../', '..\\'], '', $path);

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);

    return Response::make($file, 200, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Redirect to student login by default (homepage)
    return redirect()->route('student.login');
});

// Admin/Employee Login Routes
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Fallback 'login' route for legacy compatibility
Route::get('/login', function () {
    return redirect()->route('student.login');
})->name('login');

// Student Login Routes
Route::get('/student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login.submit');
Route::post('/student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

// Student Password Reset Routes
Route::get('/student/password/reset', [StudentPasswordResetController::class, 'showLinkRequestForm'])->name('student.password.request');
Route::post('/student/password/email', [StudentPasswordResetController::class, 'sendResetLinkEmail'])->name('student.password.email');
Route::get('/student/password/reset/{token}', [StudentPasswordResetController::class, 'showResetForm'])->name('student.password.reset');
Route::post('/student/password/reset', [StudentPasswordResetController::class, 'reset'])->name('student.password.update');

// Student Registration Routes
Route::get('/student/register', [StudentRegisterController::class, 'showRegistrationForm'])->name('student.register.form');
Route::post('/student/register', [StudentRegisterController::class, 'register'])->name('student.register');
Route::get('/student/registration/success', [StudentRegisterController::class, 'success'])->name('student.registration.success');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin/Employee Profile Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('admin.profile.show');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password.update');
    Route::post('/profile/photo', [AdminProfileController::class, 'uploadPhoto'])->name('admin.profile.photo.upload');
    Route::delete('/profile/photo', [AdminProfileController::class, 'deletePhoto'])->name('admin.profile.photo.delete');
});

// Reports Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Student Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/my-students', [StudentController::class, 'myStudents'])->name('students.my-students');

    // CSV Export Routes
    Route::get('/students/export-form', [StudentController::class, 'exportForm'])->name('students.export.form');
    Route::post('/students/export', [StudentController::class, 'export'])->name('students.export');

    Route::resource('students', StudentController::class);
    Route::get('/students/{student}/approve', [StudentController::class, 'showApproveForm'])->name('students.approve.form');
    Route::post('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('/students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');

    // Student Profile Management (Admin/Staff)
    Route::get('/students/{student}/profile', [StudentProfileController::class, 'show'])->name('students.profile.show');
    Route::get('/students/{student}/profile/edit', [StudentProfileController::class, 'edit'])->name('students.profile.edit');
    Route::put('/students/{student}/profile', [StudentProfileController::class, 'update'])->name('students.profile.update');
    Route::post('/students/{student}/profile/photo', [StudentProfileController::class, 'uploadPhoto'])->name('students.profile.photo.upload');
    Route::delete('/students/{student}/profile/photo', [StudentProfileController::class, 'deletePhoto'])->name('students.profile.photo.delete');

    // Student Payment Routes
    Route::get('/students/{student}/payments', [StudentPaymentController::class, 'index'])->name('students.payments.index');
    Route::get('/students/{student}/payments/create', [StudentPaymentController::class, 'create'])->name('students.payments.create');
    Route::post('/students/{student}/payments', [StudentPaymentController::class, 'store'])->name('students.payments.store');
    Route::get('/students/{student}/payments/{payment}/edit', [StudentPaymentController::class, 'edit'])->name('students.payments.edit');
    Route::put('/students/{student}/payments/{payment}', [StudentPaymentController::class, 'update'])->name('students.payments.update');
    Route::delete('/students/{student}/payments/{payment}', [StudentPaymentController::class, 'destroy'])->name('students.payments.destroy');
});

// Follow-up Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('follow-ups', FollowUpController::class)->except(['index', 'show']);
    Route::get('/students/{student}/follow-ups', [FollowUpController::class, 'index'])->name('students.follow-ups');
});

// Checklist Item Routes (Admin/Employee only)
Route::middleware(['auth', 'can:create checklists'])->group(function () {
    Route::resource('checklist-items', ChecklistItemController::class);
    Route::post('checklist-items/reorder', [ChecklistItemController::class, 'reorder'])->name('checklist-items.reorder');
});

// Contact Submission Routes (Admin/Employee only)
Route::middleware(['auth'])->prefix('contact-submissions')->group(function () {
    Route::get('/', [ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
    Route::get('/{contactSubmission}', [ContactSubmissionController::class, 'show'])->name('contact-submissions.show');
    Route::put('/{contactSubmission}/status', [ContactSubmissionController::class, 'updateStatus'])->name('contact-submissions.update-status');
    Route::post('/{contactSubmission}/assign', [ContactSubmissionController::class, 'assign'])->name('contact-submissions.assign');
    Route::post('/{contactSubmission}/notes', [ContactSubmissionController::class, 'addNotes'])->name('contact-submissions.add-notes');
    Route::delete('/{contactSubmission}', [ContactSubmissionController::class, 'destroy'])->name('contact-submissions.destroy');
});

// User Management Routes (Super Admin only)
Route::middleware(['auth', 'role:Super Admin'])->prefix('users')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
});

// Role & Permission Management Routes (Super Admin only)
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin/roles')->name('admin.roles.')->group(function () {
    Route::get('/', [RoleManagementController::class, 'index'])->name('index');
    Route::get('/create', [RoleManagementController::class, 'create'])->name('create');
    Route::post('/', [RoleManagementController::class, 'store'])->name('store');
    Route::get('/{role}', [RoleManagementController::class, 'show'])->name('show');
    Route::get('/{role}/edit', [RoleManagementController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleManagementController::class, 'update'])->name('update');
    Route::delete('/{role}', [RoleManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{role}/clone', [RoleManagementController::class, 'clone'])->name('clone');
    Route::post('/sync-permissions', [RoleManagementController::class, 'syncPermissions'])->name('sync-permissions');
    Route::get('/users/{user}/permissions', [RoleManagementController::class, 'userPermissions'])->name('user-permissions');
    Route::put('/users/{user}/permissions', [RoleManagementController::class, 'updateUserPermissions'])->name('update-user-permissions');
});

// Email Settings Routes (Super Admin only)
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin/email-settings')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])->name('admin.email-settings.index');
    Route::put('/update', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'update'])->name('admin.email-settings.update');
    Route::get('/test', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'testForm'])->name('admin.email-settings.test-form');
    Route::post('/test', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'sendTest'])->name('admin.email-settings.send-test');
    Route::post('/test-connection', [\App\Http\Controllers\Admin\EmailSettingsController::class, 'testConnection'])->name('admin.email-settings.test-connection');
});

// Evaluation Question Management Routes (Super Admin only)
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin/evaluation-questions')->name('admin.evaluation-questions.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'store'])->name('store');
    Route::get('/{evaluationQuestion}/edit', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'edit'])->name('edit');
    Route::put('/{evaluationQuestion}', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'update'])->name('update');
    Route::delete('/{evaluationQuestion}', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'destroy'])->name('destroy');
    Route::patch('/{evaluationQuestion}/toggle-status', [\App\Http\Controllers\Admin\EvaluationQuestionController::class, 'toggleStatus'])->name('toggle-status');
});

// Consultant Evaluation Management Routes (Super Admin only)
Route::middleware(['auth', 'role:Super Admin'])->prefix('admin/consultant-evaluations')->name('admin.consultant-evaluations.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ConsultantEvaluationController::class, 'index'])->name('index');
    Route::get('/export', [\App\Http\Controllers\Admin\ConsultantEvaluationController::class, 'export'])->name('export');
    Route::get('/{consultant}', [\App\Http\Controllers\Admin\ConsultantEvaluationController::class, 'show'])->name('show');
});

// University Management Routes (Admin/Employee only)
Route::middleware(['auth'])->group(function () {
    Route::resource('universities', UniversityController::class);
});

// Program Management Routes (Admin/Employee only)
Route::middleware(['auth'])->group(function () {
    Route::resource('programs', ProgramController::class);
    Route::get('/universities/{university}/programs', [ProgramController::class, 'byUniversity'])->name('universities.programs');

    // Student Visits Routes
    Route::resource('student-visits', StudentVisitController::class);

    // Activity Logs Routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/students/{student}/activity-logs', [ActivityLogController::class, 'studentLogs'])->name('students.activity-logs');
});

// Student Portal Routes
Route::middleware(['auth'])->prefix('student')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentChecklistController::class, 'dashboard'])->name('student.dashboard');

    // Submit Documents (formerly checklist)
    Route::get('/documents', [StudentChecklistController::class, 'index'])->name('student.documents');
    Route::post('/checklist/{checklistItem}/upload', [StudentChecklistController::class, 'uploadDocument'])->name('student.checklist.upload');
    Route::delete('/checklist/{studentChecklist}', [StudentChecklistController::class, 'deleteDocument'])->name('student.checklist.delete');
    Route::post('/checklist/{studentChecklist}/resubmit', [StudentChecklistController::class, 'resubmitDocument'])->name('student.checklist.resubmit');

    // Profile Management - Enhanced
    Route::get('/profile', [StudentProfileController::class, 'edit'])->name('student.profile');
    Route::get('/profile/edit', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
    Route::post('/profile/photo', [StudentProfileController::class, 'uploadPhoto'])->name('student.profile.photo.upload');
    Route::delete('/profile/photo', [StudentProfileController::class, 'deletePhoto'])->name('student.profile.photo.delete');

    // FAQ
    Route::get('/faq', [StudentChecklistController::class, 'faq'])->name('student.faq');

    // Emergency Contact
    Route::get('/emergency-contact', [StudentChecklistController::class, 'emergencyContact'])->name('student.emergency-contact');
    Route::post('/contact/submit', [StudentChecklistController::class, 'submitContact'])->name('student.contact.submit');

    // My Program
    Route::get('/program', [StudentChecklistController::class, 'showProgram'])->name('student.program');

    // Universities Info
    Route::get('/universities', [StudentChecklistController::class, 'showUniversities'])->name('student.universities');

    // Settings
    Route::get('/settings', [StudentChecklistController::class, 'showSettings'])->name('student.settings');
    Route::put('/settings', [StudentChecklistController::class, 'updateSettings'])->name('student.settings.update');

    // Consultant Evaluation Routes (Student only)
    Route::get('/consultant-evaluation', [\App\Http\Controllers\Student\ConsultantEvaluationController::class, 'index'])->name('student.consultant-evaluation.index');
    Route::post('/consultant-evaluation', [\App\Http\Controllers\Student\ConsultantEvaluationController::class, 'store'])->name('student.consultant-evaluation.store');
});

// Document Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/students/{student}/documents', [DocumentController::class, 'studentDocuments'])->name('students.documents');
    Route::post('/students/{student}/documents/merge', [DocumentController::class, 'mergeDocuments'])->name('students.documents.merge');
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
    Route::get('/students/{student}/documents/{document}/download', [DocumentController::class, 'download'])->name('students.documents.download')->scopeBindings();
    Route::get('/students/{student}/documents/{document}/view', [DocumentController::class, 'view'])->name('students.documents.view')->scopeBindings();
    Route::get('/students/{student}/documents/merge-all', [DocumentController::class, 'mergeAllApprovedDocuments'])->name('students.documents.mergeAll')->scopeBindings();
    Route::get('/api/documents/{document}/data', [DocumentController::class, 'getData'])->name('documents.data');
    Route::delete('/students/{student}/documents/{document}', [DocumentController::class, 'destroy'])->name('students.documents.destroy')->scopeBindings();
    Route::post('/documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Student Checklist Document Approval/Rejection
    Route::post('/student-checklist/{studentChecklist}/approve', [StudentChecklistController::class, 'approveDocument'])->name('student.checklist.approve');
    Route::post('/student-checklist/{studentChecklist}/reject', [StudentChecklistController::class, 'rejectDocument'])->name('student.checklist.reject');
});

// ========================================
// OFFICE MANAGEMENT SYSTEM ROUTES
// ========================================
Route::middleware(['auth'])->prefix('office')->name('office.')->group(function () {

    // Daily Reports Module
    Route::prefix('daily-reports')->name('daily-reports.')->group(function () {
        Route::get('/', [DailyReportController::class, 'index'])->name('index');
        Route::get('/create', [DailyReportController::class, 'create'])->name('create');
        Route::post('/', [DailyReportController::class, 'store'])->name('store');
        Route::get('/{dailyReport}', [DailyReportController::class, 'show'])->name('show');
        Route::get('/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('edit');
        Route::put('/{dailyReport}', [DailyReportController::class, 'update'])->name('update');
        Route::delete('/{dailyReport}', [DailyReportController::class, 'destroy'])->name('destroy');
        
        // New workflow actions
        Route::post('/{dailyReport}/submit', [DailyReportController::class, 'submit'])->name('submit');
        Route::post('/{dailyReport}/approve', [DailyReportController::class, 'approve'])->name('approve');
        Route::post('/{dailyReport}/reject', [DailyReportController::class, 'reject'])->name('reject');
        Route::post('/{dailyReport}/review', [DailyReportController::class, 'review'])->name('review');
        
        // Comments and attachments
        Route::post('/{dailyReport}/comments', [DailyReportController::class, 'addComment'])->name('comments');
        Route::post('/{dailyReport}/attachments', [DailyReportController::class, 'uploadAttachment'])->name('attachments');
        
        // PDF Export (Super Admin Only)
        Route::get('/export/pdf-form', [DailyReportController::class, 'showExportForm'])->name('export-form');
        Route::post('/export/pdf', [DailyReportController::class, 'exportPDF'])->name('export-pdf');
    });

    // Department Management Module (Super Admin Only)
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::post('/{department}/assign-user', [DepartmentController::class, 'assignUser'])->name('assign-user');
        Route::delete('/{department}/remove-user', [DepartmentController::class, 'removeUser'])->name('remove-user');
        Route::put('/{department}/update-manager', [DepartmentController::class, 'updateManager'])->name('update-manager');
    });
});

// Home route redirect to dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');
