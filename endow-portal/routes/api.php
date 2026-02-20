<?php

use App\Http\Controllers\Api\StudentProfileApiController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Notification routes moved to web.php for proper session authentication

// Student Profile API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Student Profile CRUD
    Route::get('/student/profile', [StudentProfileApiController::class, 'index'])->name('api.student.profile.index');
    Route::post('/student/profile', [StudentProfileApiController::class, 'store'])->name('api.student.profile.store');
    Route::get('/student/profile/{student}', [StudentProfileApiController::class, 'show'])->name('api.student.profile.show');
    Route::put('/student/profile/{student}', [StudentProfileApiController::class, 'update'])->name('api.student.profile.update');
    Route::delete('/student/profile/{student}', [StudentProfileApiController::class, 'destroy'])->name('api.student.profile.destroy');
    
    // Profile Photo Management
    Route::post('/student/profile/{student}/photo', [StudentProfileApiController::class, 'uploadPhoto'])->name('api.student.profile.photo.upload');
    Route::delete('/student/profile/{student}/photo', [StudentProfileApiController::class, 'deletePhoto'])->name('api.student.profile.photo.delete');
});

