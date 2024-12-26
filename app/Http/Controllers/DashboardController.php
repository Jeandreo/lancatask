<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Obtém minhas tarefas
        $tasks = Task::where('designated_id', Auth::id())->get();
        $users = User::where('status', true)->get();

        // RETURN VIEW WITH DATA
        return view('pages.dashboard')->with([
           'tasks' => $tasks,
           'users' => $users,
        ]);
    }
}
