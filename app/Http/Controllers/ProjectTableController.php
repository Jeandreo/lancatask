<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProjectTableController extends Controller
{
    public function processing(Request $request)
    {
        $query = Project::query()
            ->with(['type:id,name', 'users:id,name'])
            ->select(['id', 'name', 'type_is', 'type_id', 'status'])
            ->orderBy('status', 'desc')
            ->orderBy('id', 'desc');

        return DataTables::of($query)
            ->addColumn('name', fn ($row) => '<a href="' . route('projects.show', $row->id) . '" class="text-gray-700 fw-bold text-hover-primary fs-6">' . e($row->name) . '</a>')
            ->addColumn('type_is', fn ($row) => $row->type_is === 'time' ? '<span class="badge badge-light-info">Time</span>' : '<span class="badge badge-light-success">Pessoal</span>')
            ->addColumn('group', fn ($row) => '<span class="badge badge-light-primary">' . e(optional($row->type)->name ?? '-') . '</span>')
            ->addColumn('members', function ($row) {
                $html = '<div class="symbol-group symbol-hover flex-nowrap">';
                foreach ($row->users as $user) {
                    $html .= '<div class="symbol symbol-30px symbol-circle" data-bs-toggle="tooltip" data-bs-original-title="' . e($user->name) . '"><img alt="Pic" src="' . findImage('users/photos/' . $user->id . '.jpg') . '" class="object-fit-cover"></div>';
                }
                return $html . '</div>';
            })
            ->addColumn('tasks', fn ($row) => '<span class="badge badge-light-primary">' . $row->tasksCount('checked') . ' / ' . $row->tasksCount() . '</span>')
            ->addColumn('status', fn ($row) => $row->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>')
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('projects.edit', $row->id) . '"><i class="fas fa-edit" title="Editar"></i></a>'
                    . '<a href="' . route('projects.duplicate', $row->id) . '"><i class="fa-solid fa-copy" title="Copiar"></i></a>'
                    . '<a href="' . route('projects.destroy', $row->id) . '">' . $toggleIcon . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('projects.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="projeto"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['name', 'type_is', 'group', 'members', 'tasks', 'status', 'actions'])
            ->toJson();
    }
}
