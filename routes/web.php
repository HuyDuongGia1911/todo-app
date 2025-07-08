<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SetupController;

// ========================================
// ✔️ AUTH routes
// ========================================
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

// ========================================
// ✔️ APP routes (phải login)
// ========================================
Route::middleware(['auth'])->group(function () {

    // Dashboard & Task
    Route::get('/dashboard', [TaskController::class, 'dashboard']);
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    // Các route phụ
    Route::get('/plan', [TaskController::class, 'plan']);
    Route::post('/plan', [TaskController::class, 'storePlan']);
    Route::get('/deadline', [TaskController::class, 'deadline']);
    Route::get('/export', [TaskController::class, 'export']);
    Route::get('/all', [TaskController::class, 'all'])->name('tasks.all');


    // ⚠️ Setup route vẫn giữ, nhưng không còn các resource riêng nữa
    Route::get('/setup', [SetupController::class, 'index']);
    Route::post('/setup', [SetupController::class, 'store']);
});


use App\Http\Controllers\Api\ShiftApiController;
use App\Http\Controllers\Api\TypeApiController;
use App\Http\Controllers\Api\TitleApiController;
use App\Http\Controllers\Api\SupervisorApiController;
use App\Http\Controllers\Api\StatusApiController;

Route::prefix('api')->middleware('auth')->group(function () {
    Route::prefix('shifts')->group(function () {
        Route::get('/', [ShiftApiController::class, 'index']);
        Route::post('/', [ShiftApiController::class, 'store']);
        Route::put('/{id}', [ShiftApiController::class, 'update']);
        Route::delete('/{id}', [ShiftApiController::class, 'destroy']);
    });

    Route::prefix('types')->group(function () {
        Route::get('/', [TypeApiController::class, 'index']);
        Route::post('/', [TypeApiController::class, 'store']);
        Route::put('/{id}', [TypeApiController::class, 'update']);
        Route::delete('/{id}', [TypeApiController::class, 'destroy']);
    });

    Route::prefix('titles')->group(function () {
        Route::get('/', [TitleApiController::class, 'index']);
        Route::post('/', [TitleApiController::class, 'store']);
        Route::put('/{id}', [TitleApiController::class, 'update']);
        Route::delete('/{id}', [TitleApiController::class, 'destroy']);
    });

    Route::prefix('supervisors')->group(function () {
        Route::get('/', [SupervisorApiController::class, 'index']);
        Route::post('/', [SupervisorApiController::class, 'store']);
        Route::put('/{id}', [SupervisorApiController::class, 'update']);
        Route::delete('/{id}', [SupervisorApiController::class, 'destroy']);
    });

    Route::prefix('statuses')->group(function () {
        Route::get('/', [StatusApiController::class, 'index']);
        Route::post('/', [StatusApiController::class, 'store']);
        Route::put('/{id}', [StatusApiController::class, 'update']);
        Route::delete('/{id}', [StatusApiController::class, 'destroy']);
    });
});
