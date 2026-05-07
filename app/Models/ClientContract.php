<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientContract extends Model
{
    protected $table = 'client_contracts';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'client_id',
        'contract_id',
        'amount',
        'start_date',
        'end_date',
        'period_in_months',
        'duration_in_months',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
