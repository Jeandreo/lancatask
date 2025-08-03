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
        $tasks = Auth::user()->tasks()
            ->where('checked', false)
            ->where('status', true)
            ->whereHas('module.project', function ($query) {
                $query->where('status', true);
            })
            ->orderBy('date', 'ASC')
            ->get();


        $users = User::where('status', 1)->get();

        // RETURN VIEW WITH DATA
        return view('pages.dashboard')->with([
           'tasks' => $tasks,
           'users' => $users,
        ]);
    }
}
