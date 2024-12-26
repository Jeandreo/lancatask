<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
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
            Route::get('/visualizar/{id}', [ProjectController::class, 'show'])->name('show');
            Route::get('/editar/{id}', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [ProjectController::class, 'update'])->name('update');
            Route::get('/desabilitar/{id}', [ProjectController::class, 'destroy'])->name('destroy');
        });
    });

    // TASKS
    Route::prefix('tarefas')->group(function () {
        Route::name('tasks.')->group(function () {
            Route::post('/', [TaskController::class, 'index'])->name('index');
            Route::post('/adicionar', [TaskController::class, 'store'])->name('store');
            Route::get('/visualizando/{id}', [TaskController::class, 'show'])->name('show');
            Route::get('/visualizando-lista/{id}', [TaskController::class, 'showOne'])->name('show.one');
            Route::post('/desabilitar', [TaskController::class, 'destroy'])->name('destroy');
            Route::get('/desabilitar/{id?}', [TaskController::class, 'destroy'])->name('destroy');
            Route::post('/exibir-subtarefas', [TaskController::class, 'showSubtasks'])->name('show.subtasks');
            Route::get('/editar/{id}', [TaskController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [TaskController::class, 'update'])->name('update');
            Route::put('/editar-ajax/{id}', [TaskController::class, 'updateAjax'])->name('update.ajax');
            Route::put('/separador/{id}', [TaskController::class, 'separator'])->name('separator');
            Route::get('/ajax/{id}', [TaskController::class, 'ajax'])->name('ajax');
            Route::post('/check', [TaskController::class, 'check'])->name('check');
            Route::put('/prioridade', [TaskController::class, 'priority'])->name('priority');
            Route::put('/designado', [TaskController::class, 'designated'])->name('designated');
            Route::put('/status', [TaskController::class, 'status'])->name('status');
            Route::put('/data', [TaskController::class, 'date'])->name('date');
            Route::put('/ordem', [TaskController::class, 'order'])->name('order');
            Route::post('/subtarefa', [TaskController::class, 'subtask'])->name('subtask');
            Route::post('/concluidas', [TaskController::class, 'checkeds'])->name('checkeds');
            Route::post('/desafio', [TaskController::class, 'challenge'])->name('challenge');
            Route::post('/prazo', [TaskController::class, 'time'])->name('time');
            Route::get('/outras/{type?}', [TaskController::class, 'others'])->name('others');
        });
    });

    // COMMENTS
    Route::prefix('modulos')->group(function () {
        Route::name('modules.')->group(function () {
            Route::post('/adicionar', [ModuleController::class, 'store'])->name('store');
            Route::post('/visualizando', [ModuleController::class, 'show'])->name('show');
            Route::put('/desabilitar/{id}', [ModuleController::class, 'destroy'])->name('destroy');
            Route::put('/editar/{id}', [ModuleController::class, 'update'])->name('update');
        });
    });

    // COMMENTS
    Route::prefix('comentarios')->group(function () {
        Route::name('comments.')->group(function () {
            Route::post('/adicionar', [CommentController::class, 'store'])->name('store');
            Route::get('/visualizando/{id}', [CommentController::class, 'show'])->name('show');
            Route::put('/desabilitar/{id}', [CommentController::class, 'destroy'])->name('destroy');
            Route::put('/editar/{id}', [CommentController::class, 'update'])->name('update');
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
            Route::put('/barra-lateral', [UserController::class, 'sidebar'])->name('sidebar');
        });
    });

    // PROFILE USER
    Route::prefix('configuracoes')->group(function () {
        Route::name('configs.')->group(function () {
            Route::post('/cke-upload', [ConfigController::class, 'CKEupload']);
        });
    });

});



require __DIR__.'/auth.php';
