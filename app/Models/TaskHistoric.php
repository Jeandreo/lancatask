<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaskHistoric extends Model
{
    use HasFactory;
    protected $table = 'tasks_historics';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'action',
        'previous_key',
        'key',
        'created_by',
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
    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'id', 'task_id');
    }

    /**
     * Get the creator associated with the tasks.
     */
    public function statusOld(): HasOne
    {
        return $this->hasOne(Status::class, 'id', 'previous_key');
    }

    /**
     * Get the creator associated with the tasks.
     */
    public function status(): HasOne
    {
        return $this->hasOne(Status::class, 'id', 'key');
    }

    /**
     * Get the creator associated with the tasks.
     */
    public function designatedOld(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'previous_key');
    }

    /**
     * Get the creator associated with the tasks.
     */
    public function designated(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'key');
    }
}
