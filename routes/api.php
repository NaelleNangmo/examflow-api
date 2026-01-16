<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\CourseUnitController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\TranscriptController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\GradeValidationController;
use App\Http\Controllers\Api\AcademicReportController;

// Routes publiques
Route::post('/auth/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/activate', [UserController::class, 'activate']);
    Route::post('/users/{id}/deactivate', [UserController::class, 'deactivate']);

    // Students
    Route::get('/students/me', [StudentController::class, 'currentStudent']);
    Route::apiResource('students', StudentController::class);
    Route::get('/students/{id}/grades', [StudentController::class, 'grades']);

    // Teachers
    Route::get('/teachers/me', [TeacherController::class, 'currentTeacher']);
    Route::apiResource('teachers', TeacherController::class);
    Route::get('/teachers/{id}/assignments', [TeacherController::class, 'assignments']);

    // Grades
    Route::apiResource('grades', GradeController::class);
    Route::post('/grades/bulk', [GradeController::class, 'bulkStore']);
    Route::post('/grades/{id}/validate', [GradeController::class, 'validate']);
    Route::post('/grades/{id}/reject', [GradeController::class, 'reject']);
    Route::get('/grades/{id}/history', [GradeController::class, 'history']);

    // Grade Validation
    Route::prefix('grades-validation')->group(function () {
        Route::get('/pedagogical-pending', [GradeValidationController::class, 'getPedagogicalValidationPending']);
        Route::get('/administrative-pending', [GradeValidationController::class, 'getAdministrativeValidationPending']);
        Route::post('/validate-pedagogical', [GradeValidationController::class, 'validatePedagogical']);
        Route::post('/validate-administrative', [GradeValidationController::class, 'validateAdministrative']);
        Route::get('/{gradeId}/status', [GradeValidationController::class, 'getValidationStatus']);
    });

    // Course Units
    Route::apiResource('course-units', CourseUnitController::class);
    Route::get('/course-units/{id}/statistics', [CourseUnitController::class, 'statistics']);

    // Results
    Route::apiResource('results', ResultController::class);
    Route::post('/results/calculate', [ResultController::class, 'calculate']);
    Route::post('/results/{id}/validate', [ResultController::class, 'validate']);

    // Transcripts
    Route::apiResource('transcripts', TranscriptController::class);
    Route::post('/transcripts/generate', [TranscriptController::class, 'generate']);
    Route::get('/transcripts/{id}/download', [TranscriptController::class, 'download']);

    // Requests
    Route::apiResource('requests', RequestController::class);
    Route::post('/requests/{id}/assign', [RequestController::class, 'assign']);
    Route::post('/requests/{id}/approve', [RequestController::class, 'approve']);
    Route::post('/requests/{id}/reject', [RequestController::class, 'reject']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Statistics
    Route::get('/statistics', [StatisticsController::class, 'dashboard']);
    Route::get('/statistics/dashboard', [StatisticsController::class, 'dashboard']);

    // Academic Reports
    Route::prefix('reports')->group(function () {
        Route::get('/statistics/by-program', [AcademicReportController::class, 'statisticsByProgram']);
        Route::get('/statistics/by-level', [AcademicReportController::class, 'statisticsByLevel']);
        Route::get('/ranking/by-program', [AcademicReportController::class, 'rankingByProgram']);
        Route::get('/ranking/by-level', [AcademicReportController::class, 'rankingByLevel']);
        Route::get('/top-students/by-course-unit', [AcademicReportController::class, 'topStudentsByCourseUnit']);
        Route::get('/export/statistics-excel', [AcademicReportController::class, 'exportStatisticsExcel']);
        Route::get('/export/statistics-pdf', [AcademicReportController::class, 'exportStatisticsPDF']);
    });

    // Departments
    Route::apiResource('departments', DepartmentController::class);

    // Programs
    Route::apiResource('programs', ProgramController::class);

    // Academic Years
    Route::apiResource('academic-years', AcademicYearController::class);

    // Semesters
    Route::apiResource('semesters', SemesterController::class);
});

