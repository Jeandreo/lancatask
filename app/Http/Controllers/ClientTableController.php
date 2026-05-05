<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClientTableController extends Controller
{
    public function processing(Request $request)
    {
        $query = Client::query()
            ->leftJoin('contracts', 'contracts.id', '=', 'clients.contract_id')
            ->select(
                'clients.id',
                'clients.name',
                'clients.email',
                'clients.document',
                'clients.person_type',
                'clients.start_date',
                'clients.contract_value',
                'clients.status',
                'contracts.name as contract_name'
            );

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('clients.name', 'like', "%{$search}%")
                    ->orWhere('clients.email', 'like', "%{$search}%")
                    ->orWhere('clients.document', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('clients.status', (int) $request->status);
        }

        if ($request->filled('person_type')) {
            $query->where('clients.person_type', $request->person_type);
        }

        if ($request->filled('contract_id')) {
            $query->where('clients.contract_id', $request->contract_id);
        }

        $query->orderBy('clients.status', 'desc')->orderBy('clients.id', 'desc');

        if ($request->filled('order_by') && $request->has('order.0.dir')) {
            $direction = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
            $column = match ($request->order_by) {
                'name' => 'clients.name',
                'contract' => 'contracts.name',
                'value' => 'clients.contract_value',
                'start_date' => 'clients.start_date',
                'status' => 'clients.status',
                default => 'clients.id',
            };

            $query->orderBy($column, $direction);
        }

        return DataTables::of($query)
            ->addColumn('name', function ($row) {
                return '<a href="' . route('clients.edit', $row->id) . '" class="text-gray-700 text-hover-primary fw-bold fs-6">' . e($row->name) . '</a>';
            })
            ->addColumn('contract', function ($row) {
                if (empty($row->contract_name)) {
                    return "<span class='badge badge-light'>-</span>";
                }

                return "<span class='text-gray-700 fw-bold fs-6'>" . e($row->contract_name) . "</span>";
            })
            ->addColumn('value', function ($row) {
                return $row->contract_value ?: '-';
            })
            ->addColumn('start_date', function ($row) {
                if (empty($row->start_date)) {
                    return "<span class='badge badge-light'>-</span>";
                }

                return "<span class='badge badge-light'>" . date('d/m/Y', strtotime($row->start_date)) . "</span>";
            })
            ->addColumn('status', function ($row) {
                if ((int) $row->status === 1) {
                    return "<span class='badge badge-light-success'>Ativo</span>";
                }

                return "<span class='badge badge-light-danger'>Inativo</span>";
            })
            ->addColumn('actions', function ($row) {
                $toggleIcon = (int) $row->status === 1
                    ? "<i class='fas fa-times-circle' title='Desativar'></i>"
                    : "<i class='fas fa-redo' title='Reativar'></i>";

                return "<div class='d-flex align-items-center icons-table'>
                            <a href='" . route('clients.edit', $row->id) . "'>
                                <i class='fas fa-edit' title='Editar'></i>
                            </a>
                            <a href='" . route('clients.destroy', $row->id) . "'>
                                {$toggleIcon}
                            </a>
                            <a href='#' class='js-delete-client' data-url='" . route('clients.delete', $row->id) . "' data-name='" . e($row->name) . "'>
                                <i class='fas fa-trash-alt text-hover-danger' title='Excluir'></i>
                            </a>
                        </div>";
            })
            ->rawColumns(['name', 'contract', 'start_date', 'status', 'actions'])
            ->toJson();
    }
}
