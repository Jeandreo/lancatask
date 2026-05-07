<?php

namespace App\Http\Controllers;

use App\Services\ClientContractBillingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialTableController extends Controller
{
    private $billingService;

    public function __construct(ClientContractBillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    private function ensureAdmin(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }
    }

    public function processing(Request $request)
    {
        $this->ensureAdmin();

        $realRows = DB::table('financial_transactions')
            ->leftJoin('financial_wallets', 'financial_wallets.id', '=', 'financial_transactions.wallet_id')
            ->leftJoin('financial_categories', 'financial_categories.id', '=', 'financial_transactions.category_id')
            ->leftJoin('clients as counterparty_client', 'counterparty_client.id', '=', 'financial_transactions.counterparty_id')
            ->leftJoin('users as counterparty_user', 'counterparty_user.id', '=', 'financial_transactions.counterparty_id')
            ->select(
                'financial_transactions.*',
                'financial_wallets.name as wallet_name',
                'financial_categories.name as category_name',
                DB::raw("CASE WHEN financial_transactions.counterparty_type = 'client' THEN counterparty_client.name WHEN financial_transactions.counterparty_type = 'user' THEN counterparty_user.name ELSE '-' END as counterparty_name")
            )
            ->get()
            ->map(function ($row) {
                $row->is_virtual = false;
                return $row;
            });

        $virtualRows = $this->billingService->getVirtualTransactionsForFinancial();

        $allRows = $realRows->concat($virtualRows);
        $filteredRows = $allRows->filter(function ($row) use ($request) {
            if ($request->filled('type') && $row->type !== $request->type) {
                return false;
            }

            if ($request->filled('wallet_id') && $row->wallet_id != $request->wallet_id) {
                return false;
            }

            if ($request->filled('category_id') && $row->category_id != $request->category_id) {
                return false;
            }

            if ($request->filled('counterparty_type') && $row->counterparty_type !== $request->counterparty_type) {
                return false;
            }

            if ($request->filled('billing_status') && $row->billing_status !== $request->billing_status) {
                return false;
            }

            if ($request->filled('filter_month')) {
                $currentDate = !empty($row->due_date) ? Carbon::parse($row->due_date) : Carbon::parse($row->date);
                if ($currentDate->format('Y-m') !== $request->filter_month) {
                    return false;
                }
            }

            $search = $request->input('search.value', '');
            if ($search !== '') {
                $haystack = [
                    $row->name,
                    $row->wallet_name,
                    $row->category_name,
                    $row->counterparty_name,
                ];

                $found = false;
                foreach ($haystack as $item) {
                    if ($item !== null && stripos($item, $search) !== false) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    return false;
                }
            }

            return true;
        });

        $orderColumnIndex = filter_var($request->input('order.0.column'), FILTER_VALIDATE_INT);
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $orderColumnKey = 'date';

        if ($orderColumnIndex !== false) {
            $orderColumnKey = $request->input('columns.' . $orderColumnIndex . '.data', 'date');
        }

        $extractors = [
            'date' => function ($row) {
                if (!empty($row->date)) {
                    return Carbon::parse($row->date)->timestamp;
                }
                return 0;
            },
            'due_date' => function ($row) {
                if (!empty($row->due_date)) {
                    return Carbon::parse($row->due_date)->timestamp;
                }
                return 0;
            },
            'name' => fn ($row) => mb_strtolower($row->name ?? ''),
            'origin_type' => fn ($row) => mb_strtolower($row->origin_type ?? ''),
            'billing_status' => fn ($row) => mb_strtolower($row->billing_status ?? ''),
            'wallet_name' => fn ($row) => mb_strtolower($row->wallet_name ?? ''),
            'category_name' => fn ($row) => mb_strtolower($row->category_name ?? ''),
            'counterparty_name' => fn ($row) => mb_strtolower($row->counterparty_name ?? ''),
            'amount' => fn ($row) => $row->amount ?? 0,
        ];

        if (!array_key_exists($orderColumnKey, $extractors)) {
            $orderColumnKey = 'date';
        }

        $sortFn = $extractors[$orderColumnKey];
        if ($orderDirection === 'desc') {
            $filteredRows = $filteredRows->sortByDesc($sortFn)->values();
        } else {
            $filteredRows = $filteredRows->sortBy($sortFn)->values();
        }

        $totalRecords = $allRows->count();
        $totalFiltered = $filteredRows->count();

        $start = filter_var($request->input('start', 0), FILTER_VALIDATE_INT);
        if ($start === false || $start < 0) {
            $start = 0;
        }

        $length = filter_var($request->input('length', 10), FILTER_VALIDATE_INT);
        if ($length === false) {
            $length = 10;
        }

        if ($length < 1) {
            $length = 10;
        }

        $rows = $filteredRows->slice($start, $length)->map(function ($row) {
            $date = !empty($row->date) ? Carbon::parse($row->date)->format('d/m/Y') : '-';
            $dueDate = !empty($row->due_date) ? Carbon::parse($row->due_date)->format('d/m/Y') : '-';
            $billingStatusRaw = $row->billing_status ?? 'pendente';
            $billingStatus = ucfirst($billingStatusRaw);
            $billingStatusClass = 'badge-light';

            if ($billingStatusRaw === 'pendente') {
                $billingStatusClass = 'badge-light-warning';
            }

            if ($billingStatusRaw === 'pago') {
                $billingStatusClass = 'badge-light-success';
            }

            if ($billingStatusRaw === 'vencido') {
                $billingStatusClass = 'badge-light-danger';
            }

            if ($billingStatusRaw === 'cancelado') {
                $billingStatusClass = 'badge-light-dark';
            }
            $color = $row->type === 'entrada' ? 'text-success' : 'text-danger';
            $amount = '<span class="fw-bold ' . $color . '">R$ ' . number_format($row->amount, 2, ',', '.') . '</span>';
            if (!empty($row->is_virtual)) {
                $name = '<span class="text-gray-900 fw-bold">' . e($row->name) . '</span>';
                $name .= ' <i class="fas fa-redo-alt text-primary ms-1" title="Recorrente"></i>';
            } else {
                $name = '<a href="#" class="text-gray-900 fw-bold text-hover-primary js-financial-edit" data-url="' . route('financial.edit', $row->id) . '" data-update-url="' . route('financial.update', $row->id) . '">' . e($row->name) . '</a>';
            }

            $actions = '<div class="d-flex align-items-center icons-table">';
            if (!empty($row->is_virtual)) {
                $actions .= '<a href="#" class="js-financial-materialize" data-client-contract-id="' . e($row->client_contract_id) . '" data-reference-period="' . e($row->reference_period) . '"><i class="fas fa-check-circle text-success" title="Marcar como pago"></i></a>';
            } else {
                $actions .= '<a href="#" class="js-financial-edit" data-url="' . route('financial.edit', $row->id) . '" data-update-url="' . route('financial.update', $row->id) . '"><i class="fas fa-edit" title="Editar"></i></a>';
                $actions .= '<a href="#" class="js-confirm-delete" data-url="' . route('financial.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="transação"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>';
            }
            $actions .= '</div>';

            return [
                'id' => $row->id,
                'date' => '<span class="text-gray-700 fw-medium">' . $date . '</span>',
                'due_date' => '<span class="text-gray-600 fw-medium">' . $dueDate . '</span>',
                'name' => $name,
                'billing_status' => '<span class="badge ' . $billingStatusClass . ' fw-semibold">' . e($billingStatus) . '</span>',
                'wallet_name' => '<span class="badge badge-light text-gray-700 fw-semibold">' . e($row->wallet_name ?? '-') . '</span>',
                'category_name' => '<span class="text-gray-700 fw-medium">' . e($row->category_name ?? '-') . '</span>',
                'counterparty_name' => '<span class="text-gray-800 fw-semibold">' . e($row->counterparty_name ?? '-') . '</span>',
                'amount' => $amount,
                'actions' => $actions,
            ];
        })->values();

        return response()->json([
            'draw' => filter_var($request->input('draw', 1), FILTER_VALIDATE_INT) ?: 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $rows,
        ]);
    }
}
