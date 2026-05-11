<?php

namespace App\Http\Controllers;

use App\Models\ClientContract;
use App\Models\Contract;
use App\Models\FinancialTransaction;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        ->whereHas('module', function ($query) {
                            $query->where('status', true);
                        })
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

    public function admin()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $year = now()->year;

        $yearTransactions = FinancialTransaction::with('category')
            ->where('status', true)
            ->where('billing_status', 'pago')
            ->whereYear('date', $year)
            ->get([
                'id',
                'type',
                'category_id',
                'date',
                'amount',
            ]);

        $monthLabels = [
            'Jan',
            'Fev',
            'Mar',
            'Abr',
            'Mai',
            'Jun',
            'Jul',
            'Ago',
            'Set',
            'Out',
            'Nov',
            'Dez',
        ];

        $monthlyRevenue = [];
        $monthlyExpense = [];
        $monthlyResult = [];

        foreach (range(1, 12) as $month) {
            $monthTransactions = $yearTransactions->filter(function ($transaction) use ($month) {
                return $transaction->date && $transaction->date->month === $month;
            });

            $revenue = $monthTransactions
                ->where('type', 'entrada')
                ->sum('amount');

            $expense = $monthTransactions
                ->where('type', 'debito')
                ->sum('amount');

            $monthlyRevenue[] = round($revenue, 2);
            $monthlyExpense[] = round($expense, 2);
            $monthlyResult[] = round($revenue - $expense, 2);
        }

        $yearRevenue = $yearTransactions
            ->where('type', 'entrada')
            ->sum('amount');

        $yearExpense = $yearTransactions
            ->where('type', 'debito')
            ->sum('amount');

        $categoryRevenue = $yearTransactions
            ->where('type', 'entrada')
            ->groupBy(function ($transaction) {
                if ($transaction->category) {
                    return $transaction->category->name;
                }

                return 'Sem categoria';
            })
            ->map(function ($transactions, $categoryName) {
                return [
                    'name' => $categoryName,
                    'amount' => round($transactions->sum('amount'), 2),
                ];
            })
            ->sortByDesc('amount')
            ->values();

        $latestAccesses = DB::table('sessions')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->select(
                'users.name',
                'users.email',
                DB::raw('MAX(sessions.last_activity) as last_activity')
            )
            ->whereNotNull('sessions.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('last_activity')
            ->limit(6)
            ->get()
            ->map(function ($access) {
                return [
                    'name' => $access->name,
                    'email' => $access->email,
                    'last_activity' => Carbon::createFromTimestamp($access->last_activity)->format('d/m/Y H:i'),
                ];
            });

        $latestTasks = DB::table('tasks')
            ->leftJoin('modules', 'modules.id', '=', 'tasks.module_id')
            ->leftJoin('projects', 'projects.id', '=', 'modules.project_id')
            ->select(
                'tasks.id',
                'tasks.name',
                'tasks.checked',
                'tasks.updated_at',
                'modules.name as module_name',
                'projects.name as project_name'
            )
            ->orderByDesc('tasks.updated_at')
            ->limit(6)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'checked' => $task->checked,
                    'updated_at' => Carbon::parse($task->updated_at)->format('d/m/Y H:i'),
                    'module_name' => $task->module_name,
                    'project_name' => $task->project_name,
                ];
            });

        return view('pages.dashboard-admin')->with([
            'year' => $year,
            'cards' => [
                'contracts' => Contract::count(),
                'active_contracts' => ClientContract::where('status', true)->count(),
                'active_projects' => Project::where('status', true)->count(),
                'gross_revenue' => 'R$ ' . number_format($yearRevenue, 2, ',', '.'),
            ],
            'barTotals' => [
                'Receitas' => round($yearRevenue, 2),
                'Despesas' => round($yearExpense, 2),
                'Resultado' => round($yearRevenue - $yearExpense, 2),
            ],
            'charts' => [
                'months' => $monthLabels,
                'monthlyRevenue' => $monthlyRevenue,
                'monthlyExpense' => $monthlyExpense,
                'monthlyResult' => $monthlyResult,
                'categoryLabels' => $categoryRevenue->pluck('name')->values(),
                'categoryRevenue' => $categoryRevenue->pluck('amount')->values(),
            ],
            'latestAccesses' => $latestAccesses,
            'latestTasks' => $latestTasks,
        ]);
    }
}
