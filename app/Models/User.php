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

    public function preferences()
    {
        return $this->hasMany(UserPreferrence::class, 'created_by');
    }

    public function groupProjects()
    {
        // Obtém os IDs dos projetos que o usuário está associado
        $projectsIds = ProjectUser::where('user_id', Auth::id())->pluck('project_id')->toArray();

        // Busca ordem que o usuário deseja para os grupos
        $orderGroupSidebar = $this->preferences()
            ->where('type', 'sidebarGroupOrder')
            ->get()
            ->pluck('value')
            ->toArray();

        // Busca ordem que o usuário deseja para os projetos
        $orderProjectSidebar = $this->preferences()
            ->where('type', 'sidebarProjectsOrder')
            ->get()
            ->pluck('value')
            ->toArray();

        // Obtém os projetos e ordena primeiro pelos grupos e depois pelos projetos
        $projects = Project::whereIn('id', $projectsIds)
            ->where('status', true)
            ->with('type')
            ->get()
            ->sortBy(function ($project) use ($orderGroupSidebar, $orderProjectSidebar) {
                // Ordena primeiro pelos grupos e depois pelos projetos
                $groupOrder = array_search($project->type_id, $orderGroupSidebar);
                $projectOrder = array_search($project->id, $orderProjectSidebar);

                return [$groupOrder, $projectOrder];
            });

        // Agrupa os projetos pelo nome do tipo e estrutura como [id => ['name', 'items']]
        $groupedProjects = $projects->groupBy(function ($project) {
            return $project->type->id; // Agrupa pelo ID do tipo
        })->map(function ($items, $id) {
            return [
                'name' => $items->first()->type->name, // Nome do tipo
                'items' => $items // Projetos pertencentes ao tipo
            ];
        });

        return $groupedProjects;
    }


}
