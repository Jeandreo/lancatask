<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractTableController extends Controller
{
    public function processing(Request $request)
    {
        $query = Contract::query()->select(['id', 'name', 'duration_in_months', 'is_open_ended', 'status'])
            ->orderBy('status', 'desc')
            ->orderBy('id', 'desc');

        return DataTables::of($query)
            ->addColumn('name', fn ($row) => '<a href="' . route('contracts.edit', $row->id) . '" class="text-gray-700 text-hover-primary fw-bold fs-6">' . e($row->name) . '</a>')
            ->addColumn('duration', function ($row) {
                if ($row->is_open_ended) {
                    return '<span class="badge badge-light-primary">Sem fim</span>';
                }

                if (empty($row->duration_in_months)) {
                    return '<span class="badge badge-light">-</span>';
                }

                return '<span class="badge badge-light-info">' . e($row->duration_in_months) . ' mês(es)</span>';
            })
            ->addColumn('status', fn ($row) => $row->status ? '<span class="badge badge-light-success">Ativo</span>' : '<span class="badge badge-light-danger">Inativo</span>')
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status ? '<i class="fas fa-times-circle" title="Desativar"></i>' : '<i class="fas fa-redo" title="Reativar"></i>';
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="' . route('contracts.edit', $row->id) . '"><i class="fas fa-edit" title="Editar"></i></a>'
                    . '<a href="' . route('contracts.destroy', $row->id) . '">' . $toggleIcon . '</a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('contracts.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="contrato"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['name', 'duration', 'status', 'actions'])
            ->toJson();
    }
}
