<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $clients = DB::table('clients')
            ->whereNotNull('contract_id')
            ->get();

        foreach ($clients as $client) {
            $alreadyExists = DB::table('client_contracts')
                ->where('client_id', $client->id)
                ->where('contract_id', $client->contract_id)
                ->where('status', true)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $contract = DB::table('contracts')->where('id', $client->contract_id)->first();

            if (!$contract) {
                continue;
            }

            $amountRaw = $client->contract_value;
            $amountNormalized = $this->normalizeAmount($amountRaw);

            if ($amountNormalized === null) {
                $amountNormalized = 0;
            }

            $startDate = $client->start_date;

            if (empty($startDate)) {
                $startDate = now()->toDateString();
            }

            DB::table('client_contracts')->insert([
                'client_id' => $client->id,
                'contract_id' => $client->contract_id,
                'amount' => $amountNormalized,
                'start_date' => $startDate,
                'end_date' => null,
                'period_in_months' => $contract->period_in_months ?: 1,
                'duration_in_months' => $contract->duration_in_months ?: 12,
                'status' => true,
                'filed_by' => null,
                'created_by' => $client->created_by,
                'updated_by' => $client->updated_by,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('client_contracts')
            ->whereIn('client_id', function ($query) {
                $query->select('id')
                    ->from('clients')
                    ->whereNotNull('contract_id');
            })
            ->delete();
    }

    private function normalizeAmount($amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        $value = preg_replace('/[^\d,\.]/', '', $amount);

        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(',', '.', $value);
        $parts = explode('.', $value);

        if (count($parts) > 2) {
            $decimal = array_pop($parts);
            $integer = implode('', $parts);
            $value = $integer . '.' . $decimal;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return number_format($value, 2, '.', '');
    }
};
