<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/task/index', [TaskController::class, 'index'])->name('task.index'); // Fetch tasks
    Route::post('/task/store', [TaskController::class, 'store'])->name('task.store'); // Create task
    Route::patch('/task/{task}/toggle', [TaskController::class, 'toggle'])->name('task.toggle'); // Toggle task completion
    Route::delete('/task/{task}', [TaskController::class, 'destroy']);
    Route::patch('/task/{task}', [TaskController::class, 'edit']);
});
