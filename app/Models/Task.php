<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $casts = ['date' => 'date'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id',
        'task_id',
        'status_id',
        'checked',
        'checked_at',
        'order',
        'priority',
        'date',
        'date_start',
        'date_end',
        'name',
        'phrase',
        'description',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the comments associated with the tasks.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tasks_participants', 'task_id', 'user_id');
    }

    /**
     * Get the comments associated with the tasks.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'task_id', 'id');
    }

    /**
     * Get the comments associated with the tasks.
     */
    public function historic(): HasMany
    {
        return $this->hasMany(TaskHistoric::class, 'task_id', 'id');
    }

    /**
     * Get the subtask associated with the tasks.
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_id', 'id');
    }

    /**
     * Get the subtask associated with the tasks.
     */
    public function father(): HasOne
    {
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

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
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    /**
     * Get the creator associated with the tasks.
     */
    public function module(): HasOne
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }

    /**
     * Get the subtask associated with the tasks.
     */
    public function statusProject(): HasOne
    {
        return $this->hasOne(Status::class, 'id', 'status_id');
    }
}
