<?php

namespace App\Services;

use App\Models\ClientContract;
use App\Models\FinancialTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClientContractBillingService
{
    private const DEFAULT_PROJECTION_MONTHS = 12;

    public function calculateInstallmentsTotal(ClientContract $clientContract): int
    {
        if ($clientContract->contract && $clientContract->contract->is_open_ended) {
            return self::DEFAULT_PROJECTION_MONTHS;
        }

        $period = max($clientContract->period_in_months, 1);
        $duration = $clientContract->duration_in_months;
        if ($duration === null || $duration < 1) {
            return 0;
        }

        $total = intdiv($duration, $period);

        if ($total < 0) {
            return 0;
        }

        return $total;
    }

    public function generateRecurringTransactions(ClientContract $clientContract, $createdBy): void
    {
        $contract = $clientContract->contract;

        if (!$contract || !$contract->wallet_id || !$contract->category_id) {
            return;
        }

        $referencePeriods = $this->buildReferencePeriods($clientContract);

        foreach ($referencePeriods as $referencePeriod) {
            $competenceDate = Carbon::createFromFormat('Y-m', $referencePeriod)->startOfMonth();
            $dueDate = $this->buildDueDate($competenceDate, $clientContract->client->payment_day);

            $alreadyExists = FinancialTransaction::where('client_contract_id', $clientContract->id)
                ->where('reference_period', $referencePeriod)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            FinancialTransaction::create([
                'type' => 'entrada',
                'origin_type' => 'recorrente',
                'billing_status' => 'pendente',
                'name' => $contract->name,
                'wallet_id' => $contract->wallet_id,
                'category_id' => $contract->category_id,
                'client_id' => $clientContract->client_id,
                'client_contract_id' => $clientContract->id,
                'counterparty_type' => 'client',
                'counterparty_id' => $clientContract->client_id,
                'date' => $dueDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'amount' => $clientContract->amount,
                'reference_period' => $referencePeriod,
                'description' => 'Cobrança recorrente do contrato ' . $contract->name,
                'status' => true,
                'created_by' => $createdBy,
            ]);
        }
    }

    public function getVirtualTransactionsForClient(int $clientId): Collection
    {
        $contracts = ClientContract::with(['contract.wallet', 'contract.category', 'client'])
            ->where('client_id', $clientId)
            ->where('status', true)
            ->get();

        return $this->buildVirtualTransactionsFromContracts($contracts);
    }

    public function getVirtualTransactionsForFinancial(): Collection
    {
        $contracts = ClientContract::with(['contract.wallet', 'contract.category', 'client'])
            ->where('status', true)
            ->get();

        return $this->buildVirtualTransactionsFromContracts($contracts);
    }

    public function materializeReferencePeriod(ClientContract $clientContract, string $referencePeriod, $createdBy, string $billingStatus = 'pendente'): FinancialTransaction
    {
        $contract = $clientContract->contract;
        $competenceDate = Carbon::createFromFormat('Y-m', $referencePeriod)->startOfMonth();
        $dueDate = $this->buildDueDate($competenceDate, $clientContract->client->payment_day);

        $transaction = FinancialTransaction::where('client_contract_id', $clientContract->id)
            ->where('reference_period', $referencePeriod)
            ->first();

        if ($transaction) {
            if ($billingStatus === 'pago' && $transaction->billing_status !== 'pago') {
                $transaction->billing_status = 'pago';
                $transaction->paid_at = now();
                $transaction->updated_by = $createdBy;
                $transaction->save();
            }

            return $transaction;
        }

        $paidAt = null;
        if ($billingStatus === 'pago') {
            $paidAt = now();
        }

        return FinancialTransaction::create([
            'type' => 'entrada',
            'origin_type' => 'recorrente',
            'billing_status' => $billingStatus,
            'name' => $contract->name,
            'wallet_id' => $contract->wallet_id,
            'category_id' => $contract->category_id,
            'client_id' => $clientContract->client_id,
            'client_contract_id' => $clientContract->id,
            'counterparty_type' => 'client',
            'counterparty_id' => $clientContract->client_id,
            'date' => $dueDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'paid_at' => $paidAt,
            'amount' => $clientContract->amount,
            'reference_period' => $referencePeriod,
            'description' => 'Cobrança recorrente do contrato ' . $contract->name,
            'status' => true,
            'created_by' => $createdBy,
        ]);
    }

    private function buildReferencePeriods(ClientContract $clientContract): array
    {
        $period = max($clientContract->period_in_months, 1);
        $startDate = Carbon::parse($clientContract->start_date)->startOfMonth();

        $total = 1;
        if ($clientContract->contract && $clientContract->contract->is_open_ended) {
            $total = self::DEFAULT_PROJECTION_MONTHS;
        } else {
            $duration = $clientContract->duration_in_months;
            if ($duration === null || $duration < 1) {
                return [];
            }

            $total = intdiv($duration, $period);
            if ($total < 1) {
                return [];
            }
        }

        $periods = [];
        for ($index = 0; $index < $total; $index++) {
            $periods[] = $startDate->copy()->addMonths($period * $index)->format('Y-m');
        }

        return $periods;
    }

    private function buildVirtualTransactionsFromContracts(Collection $contracts): Collection
    {
        $virtualRows = collect();

        foreach ($contracts as $clientContract) {
            if (!$clientContract->contract || !$clientContract->contract->is_open_ended) {
                continue;
            }

            if (!$clientContract->contract->wallet_id || !$clientContract->contract->category_id) {
                continue;
            }

            $referencePeriods = $this->buildReferencePeriods($clientContract);

            foreach ($referencePeriods as $referencePeriod) {
                $exists = FinancialTransaction::where('client_contract_id', $clientContract->id)
                    ->where('reference_period', $referencePeriod)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $competenceDate = Carbon::createFromFormat('Y-m', $referencePeriod)->startOfMonth();
                $dueDate = $this->buildDueDate($competenceDate, $clientContract->client->payment_day);

                $virtualRows->push((object) [
                    'id' => 'virtual-' . $clientContract->id . '-' . $referencePeriod,
                    'is_virtual' => true,
                    'virtual_key' => $clientContract->id . '|' . $referencePeriod,
                    'type' => 'entrada',
                    'origin_type' => 'recorrente',
                    'billing_status' => 'pendente',
                    'name' => $clientContract->contract->name,
                    'wallet_id' => $clientContract->contract->wallet_id,
                    'category_id' => $clientContract->contract->category_id,
                    'wallet_name' => $clientContract->contract->wallet->name ?? '-',
                    'category_name' => $clientContract->contract->category->name ?? '-',
                    'client_id' => $clientContract->client_id,
                    'client_contract_id' => $clientContract->id,
                    'counterparty_type' => 'client',
                    'counterparty_id' => $clientContract->client_id,
                    'counterparty_name' => $clientContract->client->name ?? '-',
                    'date' => $dueDate->copy(),
                    'due_date' => $dueDate->copy(),
                    'paid_at' => null,
                    'amount' => $clientContract->amount,
                    'reference_period' => $referencePeriod,
                    'description' => 'Cobrança projetada do contrato ' . $clientContract->contract->name,
                    'status' => true,
                    'created_at' => now(),
                ]);
            }
        }

        return $virtualRows;
    }

    private function buildDueDate(Carbon $baseDate, $paymentDay): Carbon
    {
        $day = 1;

        if ($paymentDay !== null && $paymentDay !== '') {
            $normalized = filter_var($paymentDay, FILTER_VALIDATE_INT);
            if ($normalized !== false) {
                $day = max(min($normalized, 28), 1);
            }
        }

        return $baseDate->copy()->day($day);
    }
}
