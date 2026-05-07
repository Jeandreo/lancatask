<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UserTableController extends Controller
{
    public function processing(Request $request)
    {
        if (!Auth::user()->canManage()) return redirect()->back();

        $query = User::query()
            ->with('position:id,name')
            ->whereIn('status', [0, 1, 2])
            ->select(['id', 'name', 'role', 'position_id', 'status']);

        $orderColumnIndex = filter_var($request->input('order.0.column'), FILTER_VALIDATE_INT);
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if ($orderColumnIndex !== false) {
            $orderColumnKey = $request->input('columns.' . $orderColumnIndex . '.data');
            $column = match ($orderColumnKey) {
                'name' => 'name',
                'group' => 'role',
                'position' => 'position_id',
                'status' => 'status',
                default => 'id',
            };

            $query->orderBy($column, $orderDirection);
        } else {
            $query->orderBy('status', 'desc')->orderBy('id', 'desc');
        }

        return DataTables::of($query)
            ->addColumn('name', fn ($row) => '<a href="' . route('users.edit', $row->id) . '" class="text-gray-700 fw-bold text-hover-primary fs-6"><img src="' . findImage('users/photos/' . $row->id . '.jpg') . '" class="w-30px h-30px rounded me-2 object-fit-cover">' . e($row->name) . '</a>')
            ->addColumn('group', fn ($row) => '<span class="badge badge-light-primary">' . e($row->role) . '</span>')
            ->addColumn('position', fn ($row) => '<span class="badge badge-light-info">' . e(optional($row->position)->name ?? '-') . '</span>')
            ->addColumn('status', function ($row) {
                if ($row->status == 1) return '<span class="badge badge-light-success">Ativo</span>';
                if ($row->status == 0) return '<span class="badge badge-light-warning">Inativo</span>';
                return '<span class="badge badge-light-danger">Excluído</span>';
            })
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status == 1 ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('users.edit', $row->id) . '"><i class="fas fa-edit" title="Editar"></i></a>'
                    . '<a href="' . route('users.destroy', $row->id) . '">' . $toggleIcon . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('users.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="usuário"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['name', 'group', 'position', 'status', 'actions'])
            ->toJson();
    }
}
