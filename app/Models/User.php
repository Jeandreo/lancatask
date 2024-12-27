<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'sidebar',
        'sounds',
        'position_id',
        'email',
        'password',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The brands that belong to the subbrands.
     */
    public function position(): HasOne
    {
        return $this->hasOne(UserPosition::class, 'id', 'position_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'tasks_participants');
    }

    public function groupProjects()
    {
        // Obtém os IDs dos projetos que o usuário está associado
        $projectsIds = ProjectUser::where('user_id', Auth::id())->pluck('project_id')->toArray();

        // Obtém os projetos ativos com os tipos carregados
        $projects = Project::whereIn('id', $projectsIds)
            ->where('status', true)
            ->with('type') // Carrega a relação de tipo
            ->get();

        // Agrupa os projetos pelo nome do tipo
        $groupedProjects = $projects->groupBy(function ($project) {
            return $project->type->name;
        });

        return $groupedProjects;
    }

}
