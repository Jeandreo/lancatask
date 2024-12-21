<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

    // CATEGORIES
    Route::prefix('projetos')->group(function () {
        Route::name('projects.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::get('/adicionar', [ProjectController::class, 'create'])->name('create');
            Route::post('/adicionar', [ProjectController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [ProjectController::class, 'update'])->name('update');
        });
    });

    // CATEGORIES
    Route::prefix('tarefas')->group(function () {
        Route::name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::get('/adicionar', [TaskController::class, 'create'])->name('create');
            Route::post('/adicionar', [TaskController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [TaskController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [TaskController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [TaskController::class, 'update'])->name('update');
        });
    });

});



require __DIR__.'/auth.php';
