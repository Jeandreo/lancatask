<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    use HasFactory;
    protected $table = 'contracts';

    protected $casts = [
        'is_open_ended' => 'boolean',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'period_in_months',
        'duration_in_months',
        'is_open_ended',
        'wallet_id',
        'category_id',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the creator associated with the tasks.
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(FinancialWallet::class, 'wallet_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }
}
