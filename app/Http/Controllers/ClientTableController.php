<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ClientTableController extends Controller
{
    public function processing(Request $request)
    {
        $activeClientContractSubquery = "
            SELECT cc.client_id, cc.contract_id, cc.amount
            FROM client_contracts cc
            INNER JOIN (
                SELECT client_id, MAX(id) as max_id
                FROM client_contracts
                WHERE status = 1
                GROUP BY client_id
            ) current_contract ON current_contract.max_id = cc.id
        ";

        $query = Client::query()
            ->leftJoin(DB::raw('(' . $activeClientContractSubquery . ') as active_contract'), 'active_contract.client_id', '=', 'clients.id')
            ->leftJoin('contracts', 'contracts.id', '=', 'active_contract.contract_id')
            ->select(
                'clients.id',
                'clients.name',
                'clients.email',
                'clients.document',
                'clients.person_type',
                'clients.start_date',
                'clients.status',
                'active_contract.contract_id as active_contract_id',
                'active_contract.amount as active_contract_amount',
                'contracts.name as contract_name'
            );

        $search = $request->input('search.value', '');
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('clients.name', 'like', "%{$search}%")
                    ->orWhere('clients.email', 'like', "%{$search}%")
                    ->orWhere('clients.document', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('clients.status', $request->status);
        }

        if ($request->filled('person_type')) {
            $query->where('clients.person_type', $request->person_type);
        }

        if ($request->filled('contract_id')) {
            $query->where('active_contract.contract_id', $request->contract_id);
        }

        $query->orderBy('clients.status', 'desc')->orderBy('clients.id', 'desc');

        if ($request->filled('order_by') && $request->has('order.0.dir')) {
            $direction = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
            $column = match ($request->order_by) {
                'name' => 'clients.name',
                'contract' => 'contracts.name',
                'value' => 'active_contract.amount',
                'start_date' => 'clients.start_date',
                'status' => 'clients.status',
                default => 'clients.id',
            };

            $query->orderBy($column, $direction);
        }

        return DataTables::of($query)
            ->addColumn('name', function ($row) {
                return '<a href="' . route('clients.edit', $row->id) . '" class="text-gray-800 text-hover-primary fw-bold fs-6">' . e($row->name) . '</a>';
            })
            ->addColumn('contract', function ($row) {
                if (empty($row->contract_name)) {
                    return "<span class='badge badge-light'>-</span>";
                }

                return "<span class='text-gray-700 fw-semibold fs-6'>" . e($row->contract_name) . "</span>";
            })
            ->addColumn('value', function ($row) {
                if ($row->active_contract_amount === null || $row->active_contract_amount === '') {
                    return "<span class='text-gray-500 fw-medium'>-</span>";
                }

                return "<span class='text-gray-900 fw-bold'>R$ " . number_format($row->active_contract_amount, 2, ',', '.') . "</span>";
            })
            ->addColumn('start_date', function ($row) {
                if (empty($row->start_date)) {
                    return "<span class='badge badge-light'>-</span>";
                }

                return "<span class='badge badge-light'>" . date('d/m/Y', strtotime($row->start_date)) . "</span>";
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return "<span class='badge badge-light-success'>Ativo</span>";
                }

                return "<span class='badge badge-light-danger'>Inativo</span>";
            })
            ->addColumn('actions', function ($row) {
                $toggleIcon = $row->status == 1
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
            ->rawColumns(['name', 'contract', 'value', 'start_date', 'status', 'actions'])
            ->toJson();
    }
}
