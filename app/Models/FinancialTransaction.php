<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FinancialTransaction extends Model
{
    protected $table = 'financial_transactions';

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    protected $fillable = [
        'type',
        'origin_type',
        'billing_status',
        'name',
        'wallet_id',
        'category_id',
        'client_id',
        'client_contract_id',
        'reference_period',
        'counterparty_type',
        'counterparty_id',
        'date',
        'due_date',
        'paid_at',
        'amount',
        'description',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(FinancialWallet::class, 'id', 'wallet_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(FinancialCategory::class, 'id', 'category_id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
