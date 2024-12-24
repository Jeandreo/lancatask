<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    // // /**
    // //  * Get the brand associated with the user.
    // //  */
    // //  public function category(): HasOne
    // // {
    // //     return $this->HasOne(ProjectCategory::class, 'id', 'category_id');
    // // }

    /**
     * Get the brand associated with the user.
     */
     public function manager(): HasOne
    {
        return $this->HasOne(User::class, 'id', 'manager_id');
    }

    /**
     * Get the brand associated with the user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the brand associated with the user.
     */
    public function tasks(): HasMany
    {
        return $this->HasMany(Task::class, 'project_id', 'id');
    }

    /**
     * Get the brand associated with the user.
     */
     public function statuses(): HasMany
    {
        return $this->HasMany(Status::class, 'project_id', 'id');
    }

    /**
     * Get the brand associated with the user.
     */
     public function modules(): HasMany
    {
        return $this->HasMany(Module::class, 'project_id', 'id');
    }
}
