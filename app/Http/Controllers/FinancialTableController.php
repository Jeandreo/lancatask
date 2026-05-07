<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FinancialTableController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }
    }

    public function processing(Request $request)
    {
        $this->ensureAdmin();
        $query = DB::table('financial_transactions')
            ->leftJoin('financial_wallets', 'financial_wallets.id', '=', 'financial_transactions.wallet_id')
            ->leftJoin('financial_categories', 'financial_categories.id', '=', 'financial_transactions.category_id')
            ->leftJoin('clients as counterparty_client', 'counterparty_client.id', '=', 'financial_transactions.counterparty_id')
            ->leftJoin('users as counterparty_user', 'counterparty_user.id', '=', 'financial_transactions.counterparty_id')
            ->select(
                'financial_transactions.*',
                'financial_wallets.name as wallet_name',
                'financial_categories.name as category_name',
                DB::raw("CASE WHEN financial_transactions.counterparty_type = 'client' THEN counterparty_client.name WHEN financial_transactions.counterparty_type = 'user' THEN counterparty_user.name ELSE '-' END as counterparty_name")
            );

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('financial_transactions.name', 'like', "%{$search}%")
                    ->orWhere('financial_wallets.name', 'like', "%{$search}%")
                    ->orWhere('financial_categories.name', 'like', "%{$search}%")
                    ->orWhere('counterparty_client.name', 'like', "%{$search}%")
                    ->orWhere('counterparty_user.name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) $query->where('financial_transactions.type', $request->type);
        if ($request->filled('wallet_id')) $query->where('financial_transactions.wallet_id', $request->wallet_id);
        if ($request->filled('category_id')) $query->where('financial_transactions.category_id', $request->category_id);
        if ($request->filled('counterparty_type')) $query->where('financial_transactions.counterparty_type', $request->counterparty_type);
        if ($request->filled('origin_type')) $query->where('financial_transactions.origin_type', $request->origin_type);
        if ($request->filled('billing_status')) $query->where('financial_transactions.billing_status', $request->billing_status);
        if ($request->filled('date_start')) $query->whereDate('financial_transactions.date', '>=', $request->date_start);
        if ($request->filled('date_end')) $query->whereDate('financial_transactions.date', '<=', $request->date_end);

        $query->orderBy('financial_transactions.id', 'desc');

        return DataTables::of($query)
            ->addColumn('date', fn ($row) => date('d/m/Y', strtotime($row->date)))
            ->addColumn('due_date', fn ($row) => $row->due_date ? date('d/m/Y', strtotime($row->due_date)) : '-')
            ->addColumn('wallet_name', fn ($row) => '<span class="badge badge-light text-gray-700">' . e($row->wallet_name ?? '-') . '</span>')
            ->addColumn('origin_type', fn ($row) => ucfirst($row->origin_type ?? 'avulsa'))
            ->addColumn('billing_status', fn ($row) => ucfirst($row->billing_status ?? 'pendente'))
            ->addColumn('amount', function ($row) {
                $color = $row->type === 'entrada' ? 'text-success' : 'text-danger';
                return '<span class="fw-bold ' . $color . '">R$ ' . number_format((float) $row->amount, 2, ',', '.') . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="d-flex align-items-center icons-table">'
                    . '<a href="#" class="js-financial-edit" data-url="' . route('financial.edit', $row->id) . '" data-update-url="' . route('financial.update', $row->id) . '"><i class="fas fa-edit" title="Editar"></i></a>'
                    . '<a href="#" class="js-confirm-delete" data-url="' . route('financial.delete', $row->id) . '" data-label="' . e($row->name) . '" data-entity="transação"><i class="fas fa-trash-alt text-hover-danger" title="Excluir"></i></a>'
                    . '</div>';
            })
            ->rawColumns(['wallet_name', 'amount', 'actions'])
            ->toJson();
    }
}
