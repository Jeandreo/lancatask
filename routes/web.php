<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\StatusController;
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
            Route::get('/visualizar/{id}',      [ProjectController::class, 'show'])->name('show');
            Route::middleware('admin')->group(function () {
                Route::get('/',                     [ProjectController::class, 'index'])->name('index');
                Route::get('/adicionar',            [ProjectController::class, 'create'])->name('create');
                Route::post('/adicionar',           [ProjectController::class, 'store'])->name('store');
                Route::get('/editar/{id}',          [ProjectController::class, 'edit'])->name('edit');
                Route::put('/editar/{id}',          [ProjectController::class, 'update'])->name('update');
                Route::get('/desabilitar/{id}',     [ProjectController::class, 'destroy'])->name('destroy');
                Route::get('/duplicar/{id}',        [ProjectController::class, 'duplicate'])->name('duplicate');
                Route::get('/excluir/{id}',         [ProjectController::class, 'delete'])->name('delete');
            });
        });
    });

    // COMMENTS
    Route::prefix('modulos')->group(function () {
        Route::middleware('admin')->name('modules.')->group(function () {
            Route::get('/', [ModuleController::class, 'index'])->name('index');
            Route::post('/adicionar', [ModuleController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [ModuleController::class, 'destroy'])->name('destroy');
            Route::put('/editar/{id}', [ModuleController::class, 'update'])->name('update');
            Route::get('/carregar/{id}', [ModuleController::class, 'filter'])->name('filter');
            Route::put('/ordem/{id}', [ModuleController::class, 'order'])->name('order');
        });
    });

    // Projetos
    Route::prefix('clientes')->group(function () {
        Route::name('clients.')->group(function () {
            Route::get('/',                     [ClientController::class, 'index'])->name('index');
            Route::get('/adicionar',            [ClientController::class, 'create'])->name('create');
            Route::post('/adicionar',           [ClientController::class, 'store'])->name('store');
            Route::get('/editar/{id}',          [ClientController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}',          [ClientController::class, 'update'])->name('update');
            Route::get('/desabilitar/{id}',     [ClientController::class, 'destroy'])->name('destroy');
        });
    });
    // Projetos
    Route::middleware('admin')->prefix('contratos')->group(function () {
        Route::name('contracts.')->group(function () {
            Route::get('/',                     [ContractController::class, 'index'])->name('index');
            Route::get('/adicionar',            [ContractController::class, 'create'])->name('create');
            Route::post('/adicionar',           [ContractController::class, 'store'])->name('store');
            Route::get('/editar/{id}',          [ContractController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}',          [ContractController::class, 'update'])->name('update');
            Route::get('/desabilitar/{id}',     [ContractController::class, 'destroy'])->name('destroy');
        });
    });


    // PROFILE USER
    Route::prefix('calendario')->group(function () {

        // PROJECTS
        Route::name('agenda.')->group(function () {
            Route::get('/',                        [AgendaController::class, 'index'])->name('index');
            Route::get('/gerenciar',               [AgendaController::class, 'list'])->name('list')->middleware('admin');
            Route::post('/adicionar',              [AgendaController::class, 'store'])->name('store');
            Route::get('/visualizando/{id?}',      [AgendaController::class, 'show'])->name('show');
            Route::get('/desabilitar/{id}',        [AgendaController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}',             [AgendaController::class, 'edit'])->name('edit');
            Route::put('/editar/{id?}',            [AgendaController::class, 'update'])->name('update');
            Route::put('/calendario/{id?}',        [AgendaController::class, 'changeCalendar'])->name('calendar.update');
            Route::get('/adicionar-evento-google', [AgendaController::class, 'googleCalendar'])->name('google.event');
        });

    });

    // COMMENTS
    Route::middleware('admin')->prefix('tipos-de-projetos')->group(function () {
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
            Route::get('/', [TaskController::class, 'index'])->name('index')->middleware('admin');
            Route::get('/processar', [TaskController::class, 'processing'])->name('processing');
            Route::post('/adicionar', [TaskController::class, 'store'])->name('store');
            Route::get('/visualizando/{id}', [TaskController::class, 'show'])->name('show');
            Route::get('/visualizando-lista/{id}', [TaskController::class, 'showOne'])->name('show.one');
            Route::post('/desabilitar', [TaskController::class, 'destroy'])->name('destroy');
            Route::get('/desabilitar/{id?}', [TaskController::class, 'destroy'])->name('destroy');
            Route::get('/desabilitar-todas/{id}', [TaskController::class, 'destroyAll'])->name('destroy.all');
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
            Route::get('/participantes/{id}', [TaskController::class, 'participants'])->name('participants');
            Route::get('/adicionar-participante/{id}', [TaskController::class, 'addParticipants'])->name('add.participants');
            Route::put('/adicionar-participante/{id}', [TaskController::class, 'addParticipant'])->name('add.participant');
            Route::get('/historico/{id}', [TaskController::class, 'historic'])->name('historic');
        });
    });

    // COMMENTS
    Route::prefix('status')->group(function () {
        Route::name('statuses.')->group(function () {
            Route::get('/adicionar', [StatusController::class, 'create'])->name('create');
            Route::post('/adicionar', [StatusController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [StatusController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}', [StatusController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}', [StatusController::class, 'update'])->name('update');
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
    Route::middleware('admin')->prefix('usuarios')->group(function () {
        Route::name('users.')->group(function () {
            Route::get('/',                 [UserController::class, 'index'])->name('index');
            Route::get('/adicionar',        [UserController::class, 'create'])->name('create');
            Route::post('/adicionar',       [UserController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}',      [UserController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}',      [UserController::class, 'update'])->name('update');
            Route::get('/apagar/{id}',      [UserController::class, 'delete'])->name('delete');
        });
    });

    // Usuários
    Route::prefix('minha-conta')->group(function () {
        Route::name('account.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::put('/', [AccountController::class, 'update'])->name('update');
            Route::put('/barra-lateral', [AccountController::class, 'sidebar'])->name('sidebar');
            Route::put('/barra-lateral-ordem/{type}', [AccountController::class, 'sidebarOrder'])->name('sidebar.order');
            Route::put('/sons', [AccountController::class, 'sounds'])->name('sounds');
        });
    });


    // COMMENTS
    Route::middleware('admin')->prefix('cargos')->group(function () {
        Route::name('positions.')->group(function () {
            Route::get('/',                 [UserPositionController::class, 'index'])->name('index');
            Route::get('/adicionar',        [UserPositionController::class, 'create'])->name('create');
            Route::post('/adicionar',       [UserPositionController::class, 'store'])->name('store');
            Route::get('/desabilitar/{id}', [UserPositionController::class, 'destroy'])->name('destroy');
            Route::get('/editar/{id}',      [UserPositionController::class, 'edit'])->name('edit');
            Route::put('/editar/{id}',      [UserPositionController::class, 'update'])->name('update');
        });
    });

    // PROFILE USER
    Route::prefix('configuracoes')->group(function () {
        Route::name('configs.')->group(function () {
            Route::post('/cke-upload', [ConfigController::class, 'CKEupload']);
            Route::get('/autenticar-google', [GoogleController::class, 'redirect'])->name('google.auth');
        });
    });

    // Testar
    Route::get('/testar', [DeveloperController::class, 'test']);

});

// Autenticação com o Google
Route::get('/google/callback', [GoogleController::class, 'callback'])->name('google.callback');



require __DIR__.'/auth.php';
