<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskParticipant extends Model
{
    protected $table = 'tasks_participants';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'task_id',
    ];
}
