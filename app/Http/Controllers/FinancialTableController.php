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

        $showVirtual = $request->input('show_virtual', '1') !== '0';
        $virtualRows = collect();

        if ($showVirtual) {
            $virtualRows = $this->billingService->getVirtualTransactionsForFinancial();
        }

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

            if ($request->filled('origin_type') && $row->origin_type !== $request->origin_type) {
                return false;
            }

            if ($request->filled('billing_status') && $row->billing_status !== $request->billing_status) {
                return false;
            }

            if ($request->filled('date_start')) {
                $currentDate = !empty($row->due_date) ? Carbon::parse($row->due_date)->toDateString() : Carbon::parse($row->date)->toDateString();
                if ($currentDate < $request->date_start) {
                    return false;
                }
            }

            if ($request->filled('date_end')) {
                $currentDate = !empty($row->due_date) ? Carbon::parse($row->due_date)->toDateString() : Carbon::parse($row->date)->toDateString();
                if ($currentDate > $request->date_end) {
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
        })->sortByDesc(function ($row) {
            if (!empty($row->due_date)) {
                return Carbon::parse($row->due_date)->timestamp;
            }

            return Carbon::parse($row->date)->timestamp;
        })->values();

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
            $originType = '<span class="text-gray-700 fw-medium">' . ucfirst($row->origin_type ?? 'avulsa') . '</span>';

            if (!empty($row->is_virtual)) {
                $originType .= ' <span class="badge badge-light-primary ms-1">Projetada</span>';
            }

            $billingStatus = ucfirst($row->billing_status ?? 'pendente');
            $color = $row->type === 'entrada' ? 'text-success' : 'text-danger';
            $amount = '<span class="fw-bold ' . $color . '">R$ ' . number_format($row->amount, 2, ',', '.') . '</span>';

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
                'name' => '<span class="text-gray-900 fw-bold">' . e($row->name) . '</span>',
                'origin_type' => $originType,
                'billing_status' => '<span class="text-gray-700 fw-semibold">' . e($billingStatus) . '</span>',
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
