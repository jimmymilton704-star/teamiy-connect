<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('teamiy.api')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/dashboard', [EmployeeController::class, 'dashboard']);

        Route::get('/attendance', [EmployeeController::class, 'attendance']);
        Route::get('/attendance/status', [EmployeeController::class, 'attendanceStatus']);
        Route::post('/attendance/check-in', [EmployeeController::class, 'checkIn']);
        Route::post('/attendance/check-out', [EmployeeController::class, 'checkOut']);

        Route::get('/leaves', [EmployeeController::class, 'leaves']);
        Route::post('/leaves', [EmployeeController::class, 'storeLeave']);
        Route::post('/leaves/time', [EmployeeController::class, 'storeTimeLeave']);

        Route::get('/tada', [EmployeeController::class, 'tada']);
        Route::post('/tada', [EmployeeController::class, 'storeTada']);

        Route::get('/payroll', [EmployeeController::class, 'payroll']);

        Route::get('/resignations', [EmployeeController::class, 'resignations']);
        Route::post('/resignations', [EmployeeController::class, 'storeResignation']);

        Route::get('/team', [EmployeeController::class, 'team']);

        Route::get('/assets', [EmployeeController::class, 'assets']);
        Route::post('/assets/{assetAssignment}/return-request', [EmployeeController::class, 'requestAssetReturn']);

        Route::get('/projects', [EmployeeController::class, 'projects']);
        Route::get('/projects/{project}', [EmployeeController::class, 'project']);
        Route::patch('/tasks/{task}/toggle-status', [EmployeeController::class, 'toggleTask']);
        Route::patch('/tasks/{task}/status', [EmployeeController::class, 'updateTaskStatus']);
        Route::post('/tasks/{task}/comments', [EmployeeController::class, 'storeTaskComment']);

        Route::get('/holidays', [EmployeeController::class, 'holidays']);
        Route::get('/notices', [EmployeeController::class, 'notices']);
        Route::get('/meetings', [EmployeeController::class, 'meetings']);
        Route::get('/inbox', [EmployeeController::class, 'inbox']);

        Route::get('/profile', [EmployeeController::class, 'profile']);
        Route::match(['put', 'post'], '/profile', [EmployeeController::class, 'updateProfile']);
    });
});
