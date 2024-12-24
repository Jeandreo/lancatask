<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Module extends Model
{
    use HasFactory;
    protected $table = 'modules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'project_id',
        'color',
        'order',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the creator associated with the tasks.
     */
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    /**
     * Get the brand associated with the user.
     */
    public function statuses(): HasMany
    {
        return $this->HasMany(Status::class, 'module_id', 'id');
    }

    /**
     * Get the brand associated with the user.
     */
    public function tasks(): HasMany
    {
        return $this->HasMany(Task::class, 'module_id', 'id');
    }

}
