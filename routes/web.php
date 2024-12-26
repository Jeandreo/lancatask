<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPositionController;
use App\Models\UserPosition;
use Illuminate\Support\Facades\Route;

// AUTH
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

    // Lançamentos
    Route::get('/lancamentos', [ProjectController::class, 'releases'])->name('releases');

    // Projetos
    Route::prefix('projetos')->group(function () {
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

    // COMMENTS
    Route::prefix('tipos-de-projetos')->group(function () {
        Route::name('projects.types.')->group(function () {
            Route::get('/', [ProjectTypeController::class, 'index'])->name('index');
            Route::get('/adicionar', [ProjectTypeController::class, 'create'])->name('create');
            Route::post('/adicionar', [ProjectTypeController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [ProjectTypeController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [ProjectTypeController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [ProjectTypeController::class, 'update'])->name('update');
        });
    });

    // TASKS
    Route::prefix('tarefas')->group(function () {
        Route::name('tasks.')->group(function () {
            Route::GET('/', [TaskController::class, 'index'])->name('index');
            Route::post('/adicionar', [TaskController::class, 'store'])->name('store');
            Route::get('/visualizando/{id}', [TaskController::class, 'show'])->name('show');
            Route::get('/visualizando-lista/{id}', [TaskController::class, 'showOne'])->name('show.one');
            Route::post('/desabilitar', [TaskController::class, 'destroy'])->name('destroy');
            Route::get('/desabilitar/{id?}', [TaskController::class, 'destroy'])->name('destroy');
            Route::post('/exibir-subtarefas', [TaskController::class, 'showSubtasks'])->name('show.subtasks');
            Route::get('/editar/{id}', [TaskController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [TaskController::class, 'update'])->name('update');
            Route::get('/ajax/{id}', [TaskController::class, 'ajax'])->name('ajax');
            Route::post('/concluir', [TaskController::class, 'check'])->name('check');
            Route::put('/prioridade', [TaskController::class, 'priority'])->name('priority');
            Route::put('/designado', [TaskController::class, 'designated'])->name('designated');
            Route::put('/status', [TaskController::class, 'status'])->name('status');
            Route::put('/data', [TaskController::class, 'date'])->name('date');
            Route::put('/ordem', [TaskController::class, 'order'])->name('order');
            Route::post('/subtarefa', [TaskController::class, 'subtask'])->name('subtask');
            Route::post('/concluidas', [TaskController::class, 'checkeds'])->name('checkeds');
            Route::post('/prazo', [TaskController::class, 'time'])->name('time');
            Route::get('/historico/{id}', [TaskController::class, 'historic'])->name('historic');
        });
    });

    // COMMENTS
    Route::prefix('modulos')->group(function () {
        Route::name('modules.')->group(function () {
            Route::get('/', [ModuleController::class, 'index'])->name('index');
            Route::post('/adicionar', [ModuleController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [ModuleController::class, 'destroy'])->name('destroy');
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

    // Usuários
    Route::prefix('minha-conta')->group(function () {
        Route::name('account.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::put('/', [AccountController::class, 'update'])->name('update');
        });
    });


    // COMMENTS
    Route::prefix('cargos')->group(function () {
        Route::name('positions.')->group(function () {
            Route::get('/', [UserPositionController::class, 'index'])->name('index');
            Route::get('/adicionar', [UserPositionController::class, 'create'])->name('create');
            Route::post('/adicionar', [UserPositionController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [UserPositionController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [UserPositionController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [UserPositionController::class, 'update'])->name('update');
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
