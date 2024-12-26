<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        'type_id',
        'description',
        'start',
        'end',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the brand associated with the user.
     */
     public function modules(): HasMany
    {
        return $this->HasMany(Module::class, 'project_id', 'id');
    }

    /**
     * Get all tasks associated with the project through modules.
     */
    public function tasksCount($type = null)
    {

        // Conta tarefas dos módulos
        $count = 0;
        foreach($this->modules()->where('status', true)->get() as $module){
            $tasks = $module->tasks;

            if($type == 'checked'){
                $tasks = $tasks->where('checked', true);
            }

            $count += $tasks->count();
        }

        return $count;
    }

    /**
     * Get the brand associated with the user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'projects_users');
    }

    /**
     * Get the brand associated with the user.
     */
     public function type(): HasOne
    {
        return $this->HasOne(ProjectType::class, 'id', 'type_id');
    }

    /**
     * Get the brand associated with the user.
     */
     public function statuses(): HasMany
    {
        return $this->HasMany(Status::class, 'project_id', 'id');
    }
}
