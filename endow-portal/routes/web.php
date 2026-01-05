<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ChecklistItemController;
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
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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
    return redirect()->route('admin.login');
})->name('login');

// Student Login Routes
Route::get('/student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login.submit');
Route::post('/student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

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
});

// Document Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/students/{student}/documents', [DocumentController::class, 'studentDocuments'])->name('students.documents');
    Route::post('/students/{student}/documents/merge', [DocumentController::class, 'mergeDocuments'])->name('students.documents.merge');
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
    Route::get('/students/{student}/documents/{document}/download', [DocumentController::class, 'download'])->name('students.documents.download');
    Route::get('/students/{student}/documents/{document}/view', [DocumentController::class, 'view'])->name('students.documents.view');
    Route::get('/api/documents/{document}/data', [DocumentController::class, 'getData'])->name('documents.data');
    Route::delete('/students/{student}/documents/{document}', [DocumentController::class, 'destroy'])->name('students.documents.destroy');
    Route::post('/documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Student Checklist Document Approval/Rejection
    Route::post('/student-checklist/{studentChecklist}/approve', [StudentChecklistController::class, 'approveDocument'])->name('student.checklist.approve');
    Route::post('/student-checklist/{studentChecklist}/reject', [StudentChecklistController::class, 'rejectDocument'])->name('student.checklist.reject');
});

// Home route redirect to dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');
