<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'person_type',
        'document',
        'email',
        'contract_id',
        'contract_value',
        'phone',
        'start_date',
        'end_date',
        'zip',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
        'observations',
        'extra',
    ];

    /**
     * Get the creator associated with the tasks.
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    /**
     * Get the creator associated with the tasks.
     */
    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class, 'id', 'contract_id');
    }
}
