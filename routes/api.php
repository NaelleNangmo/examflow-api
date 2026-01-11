<?php

use App\Http\Controllers\AcademicYearController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SemesterController;

/*
|--------------------------------------------------------------------------
| TEST
|--------------------------------------------------------------------------
*/
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API IUC OK'
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| USERS (protégé)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::patch('/users/{id}/activate', [UserController::class, 'activate']);
    Route::patch('/users/{id}/desactivate', [UserController::class, 'desactivate']);
    
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('semesters', SemesterController::class);
    Route::post('semesters/{id}/set-current', [SemesterController::class, 'setCurrent']);
    Route::post('academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent']);
    /*
    |--------------------------------------------------------------------------
    | DEPARTMENTS
    |--------------------------------------------------------------------------
    */
    Route::apiResource('departments', DepartmentController::class);

    /*
    |--------------------------------------------------------------------------
    | PROGRAMS
    |--------------------------------------------------------------------------
    */
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{id}', [ProgramController::class, 'show']);
    Route::post('/programs', [ProgramController::class, 'store']);
    Route::put('/programs/{id}', [ProgramController::class, 'update']);
    Route::delete('/programs/{id}', [ProgramController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | LEVELS
    |--------------------------------------------------------------------------
    */
    Route::get('/levels', [LevelController::class, 'index']);
    Route::get('/levels/{id}', [LevelController::class, 'show']);
    Route::post('/levels', [LevelController::class, 'store']);
    Route::put('/levels/{id}', [LevelController::class, 'update']);
    Route::delete('/levels/{id}', [LevelController::class, 'destroy']);
});
