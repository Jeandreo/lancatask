<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ModuleTableController extends Controller
{
    public function processing(Request $request)
    {
        $query = Module::query()->with(['project:id,name', 'tasks:id,module_id'])->select(['id', 'name', 'project_id', 'status']);

        $orderColumnIndex = filter_var($request->input('order.0.column'), FILTER_VALIDATE_INT);
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if ($orderColumnIndex !== false) {
            $orderColumnKey = $request->input('columns.' . $orderColumnIndex . '.data');
            $column = match ($orderColumnKey) {
                'name' => 'name',
                'project' => 'project_id',
                'status' => 'status',
                default => 'id',
            };

            $query->orderBy($column, $orderDirection);
        } else {
            $query->orderBy('status', 'desc')->orderBy('id', 'desc');
        }

        return DataTables::of($query)
            ->addColumn('name', fn ($row) => '<a href="' . route('projects.show', $row->project_id) . '" class="text-gray-700 text-hover-primary fw-bold fs-6">' . e($row->name) . '</a>')
            ->addColumn('project', fn ($row) => '<a href="' . route('projects.show', $row->project_id) . '" class="badge badge-light-primary">' . e(optional($row->project)->name ?? '-') . '</a>')
            ->addColumn('tasks', fn ($row) => '<span class="badge badge-light">' . $row->tasks->count() . '</span>')
            ->addColumn('status', fn ($row) => $row->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>')
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('modules.destroy', $row->id) . '">' . $toggleIcon . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('modules.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="módulo"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['name', 'project', 'tasks', 'status', 'actions'])
            ->toJson();
    }
}
