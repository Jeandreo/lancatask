<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{

    // Define tabela e datas
    protected $table = 'projects';
    protected $casts = [
        'start' => 'date',
        'end' => 'date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'start',
        'end',
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
        return $this->HasMany(User::class);
    }
}
