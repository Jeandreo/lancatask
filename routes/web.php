<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

    // Lançamentos
    Route::prefix('lancamentos')->group(function () {
        Route::name('projects.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::get('/adicionar', [ProjectController::class, 'create'])->name('create');
            Route::post('/adicionar', [ProjectController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [ProjectController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [ProjectController::class, 'update'])->name('update');
        });
    });

    // Usuários
    Route::prefix('usuarios')->group(function () {
        Route::name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/adicionar', [UserController::class, 'create'])->name('create');
            Route::post('/adicionar', [UserController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [UserController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [UserController::class, 'update'])->name('update');
        });
    });

});



require __DIR__.'/auth.php';
