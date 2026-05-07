<?php

namespace App\Http\Controllers;

use App\Models\UserPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UserPositionTableController extends Controller
{
    public function processing(Request $request)
    {
        if (!Auth::user()->canManage()) return redirect()->back();

        $query = UserPosition::query()->withCount('users')->select(['id', 'name', 'status']);

        $orderColumnIndex = filter_var($request->input('order.0.column'), FILTER_VALIDATE_INT);
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        if ($orderColumnIndex !== false) {
            $orderColumnKey = $request->input('columns.' . $orderColumnIndex . '.data');
            $column = match ($orderColumnKey) {
                'name' => 'name',
                'status' => 'status',
                default => 'id',
            };

            $query->orderBy($column, $orderDirection);
        } else {
            $query->orderBy('status', 'desc')->orderBy('id', 'desc');
        }

        return DataTables::of($query)
            ->addColumn('name', fn ($row) => '<a href="' . route('positions.edit', $row->id) . '" class="text-gray-700 text-hover-primary fw-bold fs-6">' . e($row->name) . '</a>')
            ->addColumn('users', fn ($row) => '<span class="badge badge-light">' . $row->users_count . '</span>')
            ->addColumn('status', fn ($row) => $row->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>')
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('positions.edit', $row->id) . '"><i class="fas fa-edit" title="Editar"></i></a>'
                    . '<a href="' . route('positions.destroy', $row->id) . '">' . $toggleIcon . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('positions.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="cargo"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['name', 'users', 'status', 'actions'])
            ->toJson();
    }
}
