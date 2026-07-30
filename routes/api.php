<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\SchoolController;
use App\Http\Controllers\API\AcademicSessionController;
use App\Http\Controllers\API\SchoolClassController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\SectionController;
use App\Http\Controllers\API\TimetableController;
use App\Http\Controllers\API\TeacherController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource(
        'academic-sessions',
        AcademicSessionController::class
    );
    Route::apiResource('classes', SchoolClassController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('timetables', TimetableController::class);
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('schools', SchoolController::class);
    Route::apiResource('teachers', TeacherController::class);
    // Optional restore route
    Route::post('schools/{id}/restore', [SchoolController::class, 'restore'])->name('schools.restore');
});
