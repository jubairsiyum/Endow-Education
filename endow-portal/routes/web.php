<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentChecklistController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
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
    // Redirect to admin login by default
    return redirect()->route('admin.login');
});

// Admin/Employee Login Routes
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Student Login Routes
Route::get('/student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentLoginController::class, 'login'])->name('student.login.submit');
Route::post('/student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

// Student Registration Routes
Route::get('/student/register', [App\Http\Controllers\Auth\StudentRegisterController::class, 'showRegistrationForm'])->name('student.register.form');
Route::post('/student/register', [App\Http\Controllers\Auth\StudentRegisterController::class, 'register'])->name('student.register');
Route::get('/student/registration/success', [App\Http\Controllers\Auth\StudentRegisterController::class, 'success'])->name('student.registration.success');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Student Management Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('students', StudentController::class);
    Route::post('/students/{student}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('/students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');
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

// University Management Routes (Admin/Employee only)
Route::middleware(['auth'])->group(function () {
    Route::resource('universities', UniversityController::class);
});

// Program Management Routes (Admin/Employee only)
Route::middleware(['auth'])->group(function () {
    Route::resource('programs', ProgramController::class);
    Route::get('/universities/{university}/programs', [ProgramController::class, 'byUniversity'])->name('universities.programs');

    // Activity Logs Routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/students/{student}/activity-logs', [ActivityLogController::class, 'studentLogs'])->name('students.activity-logs');
});

// Student Checklist Routes (Student portal)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-checklist', [StudentChecklistController::class, 'index'])->name('student.checklist');
    Route::post('/my-checklist/{checklistItem}/upload', [StudentChecklistController::class, 'uploadDocument'])->name('student.checklist.upload');
    Route::delete('/my-documents/{document}', [StudentChecklistController::class, 'deleteDocument'])->name('student.document.delete');
});

// Document Management Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/students/{student}/documents', [DocumentController::class, 'studentDocuments'])->name('students.documents');
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
    Route::post('/documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});

// Home route redirect to dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');
