<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TaskTableController extends Controller
{
    public function processing(Request $request)
    {
        $data = $request->all();

        $query = DB::table('tasks')
            ->leftJoin('modules', 'tasks.module_id', '=', 'modules.id')
            ->leftJoin('statuses', 'tasks.status_id', '=', 'statuses.id')
            ->leftJoin('projects', 'modules.project_id', '=', 'projects.id');

        if (($data['searchBy'] ?? '') != '') {
            $query->where('tasks.name', 'like', "%{$data['searchBy']}%");
        }

        if (!empty($data['order'])) {
            $direction = $request->order[0]['dir'];
            $orderThis = $request->order_by;
            $column = match ($orderThis) {
                'id' => 'tasks.id',
                'name' => 'tasks.name',
                'when' => 'tasks.date_start',
                'checked' => 'tasks.checked',
                'project' => 'projects.name',
                'module' => 'modules.name',
                'status' => 'statuses.name',
                default => 'tasks.id',
            };
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('tasks.status', 'desc')->orderBy('tasks.id', 'desc');
        }

        if (isset($data['projects'])) {
            $query->whereIn('modules.project_id', $data['projects']);
        }
        if (isset($data['modules'])) {
            $query->whereIn('module_id', $data['modules']);
        }
        if (isset($data['status'])) {
            $query->whereIn('status_id', $data['status']);
        }
        if (isset($data['register'])) {
            $dates = explode(' - ', $data['register']);
            $query->whereBetween('tasks.date_start', [convertDateFormat($dates[0]), convertDateFormat($dates[1])]);
        }

        $query->groupBy(
            'tasks.id', 'tasks.name', 'tasks.date_start', 'tasks.date_end', 'tasks.checked', 'tasks.status',
            'modules.name', 'modules.color', 'projects.name', 'statuses.name', 'statuses.color'
        );

        $totalRecords = $query->select('tasks.id')->count();
        $pages = $query->paginate($request->per_page);

        $query->select(
            'tasks.id as id', 'tasks.name as name', 'tasks.date_start as date_start', 'tasks.date_end as date_end',
            'tasks.checked as checked', 'tasks.status as status', 'modules.name as module_name',
            'modules.color as module_color', 'projects.name as project_name', 'statuses.name as status_name',
            'statuses.color as status_color'
        );

        return DataTables::of($query)
            ->addColumn('id', fn ($row) => '<span class="text-gray-700 text-hover-primary fw-bold fs-6 show-task cursor-pointer" data-task="' . $row->id . '">' . $row->id . '</span>')
            ->addColumn('name', fn ($row) => '<span class="text-gray-700 text-hover-primary fw-bold fs-6 show-task cursor-pointer" data-task="' . $row->id . '">' . $row->name . '</span>')
            ->addColumn('when', function ($row) {
                if (!$row->date_start) return '<span class="text-gray-600">Sem data</span>';
                if ($row->date_start == $row->date_end) return '<span class="text-gray-600">' . date('d/m/Y', strtotime($row->date_start)) . '</span>';
                return '<span class="text-gray-600">' . date('d/m/Y', strtotime($row->date_start)) . ' até ' . date('d/m/Y', strtotime($row->date_end)) . '</span>';
            })
            ->addColumn('checked', fn ($row) => $row->checked ? '<span class="badge badge-light-success">Concluída</span>' : '<span class="badge badge-light-danger">Não concluída</span>')
            ->addColumn('project', fn ($row) => '<span class="fw-bold text-gray-900">' . $row->project_name . '</span>')
            ->addColumn('module', fn ($row) => '<span class="badge" style="background: ' . hex2rgb($row->module_color, 5) . '; color: ' . $row->module_color . '">' . $row->module_name . '</span>')
            ->addColumn('status', fn ($row) => '<span class="badge" style="background: ' . hex2rgb($row->status_color, 12) . '; color: ' . $row->status_color . '">' . $row->status_name . '</span>')
            ->addColumn('actions', function ($row) {
                $btnToogle = $row->status == 1 ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('tasks.destroy', $row->id) . '">' . $btnToogle . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('tasks.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="tarefa"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['id', 'name', 'when', 'checked', 'project', 'module', 'status', 'actions'])
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($pages->total())
            ->toJson();
    }
}
