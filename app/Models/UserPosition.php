<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPosition extends Model
{
    use HasFactory;
    protected $table = 'users_positions';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    /**
     * The brands that belong to the subbrands.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id', 'id');
    }
}
