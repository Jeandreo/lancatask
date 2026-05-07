<?php

namespace App\Services;

use App\Models\ClientContract;
use App\Models\FinancialTransaction;
use Carbon\Carbon;

class ClientContractBillingService
{
    public function calculateInstallmentsTotal(ClientContract $clientContract): int
    {
        $period = max($clientContract->period_in_months, 1);
        $duration = max($clientContract->duration_in_months, 1);

        $total = intdiv($duration, $period);

        if ($total < 1) {
            return 1;
        }

        return $total;
    }

    public function generateRecurringTransactions(ClientContract $clientContract, int|string|null $createdBy): void
    {
        $contract = $clientContract->contract;

        if (!$contract || !$contract->wallet_id || !$contract->category_id) {
            return;
        }

        $installmentsTotal = $this->calculateInstallmentsTotal($clientContract);
        $startDate = Carbon::parse($clientContract->start_date);

        for ($index = 0; $index < $installmentsTotal; $index++) {
            $competenceDate = $startDate->copy()->addMonths($clientContract->period_in_months * $index);
            $referencePeriod = $competenceDate->format('Y-m');
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

    private function buildDueDate(Carbon $baseDate, int|string|null $paymentDay): Carbon
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
