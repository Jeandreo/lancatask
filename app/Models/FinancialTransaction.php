<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FinancialTransaction extends Model
{
    protected $table = 'financial_transactions';

    protected $casts = [
        'date' => 'date',
    ];

    protected $fillable = [
        'type',
        'name',
        'wallet_id',
        'category_id',
        'client_id',
        'counterparty_type',
        'counterparty_id',
        'date',
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
