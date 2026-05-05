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

    public function isAdmin(): bool
    {
        return $this->role === 'Administrador';
    }

    public function isManager(): bool
    {
        return $this->role === 'Gerente';
    }

    public function canManage(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function groupProjects()
    {
        // Obtém os IDs dos projetos que o usuário está associado
        $menuProjectsIds = ProjectUser::where('user_id', Auth::id())->pluck('project_id')->toArray();

        // Ordem global controlada por administrador
        [$orderGroupSidebar, $orderProjectSidebar] = $this->globalSidebarOrders();

        // Obtém os projetos e ordena primeiro pelos grupos e depois pelos projetos
        $menuProjectsQuery = Project::query()
            ->where('status', true)
            ->with('type');

        if (!$this->isAdmin() && $this->isManager()) {
            // Gerente não enxerga quadros privados.
            $menuProjectsQuery
                ->whereIn('id', $menuProjectsIds)
                ->where('type_is', '!=', 'pessoal');
        } elseif (!$this->isAdmin()) {
            $menuProjectsQuery->whereIn('id', $menuProjectsIds);
        }

        $menuProjects = $menuProjectsQuery
            ->orderBy('status', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->sortBy(function ($menuProject) use ($orderGroupSidebar, $orderProjectSidebar) {
                // Ordena primeiro pelos grupos e depois pelos projetos
                $groupOrder = array_search($menuProject->type_id, $orderGroupSidebar);
                $menuProjectOrder = array_search($menuProject->id, $orderProjectSidebar);
                $groupOrder = $groupOrder === false ? 999999 : $groupOrder;
                $menuProjectOrder = $menuProjectOrder === false ? 999999 : $menuProjectOrder;

                return [$groupOrder, $menuProjectOrder];
            });

        // Agrupa os projetos pelo nome do tipo e estrutura como [id => ['name', 'items']]
        $groupedProjects = $menuProjects->groupBy(function ($menuProject) {
            return $menuProject->type->id; // Agrupa pelo ID do tipo
        })->map(function ($items, $id) {
            return [
                'name' => $items->first()->type->name, // Nome do tipo
                'items' => $items // Projetos pertencentes ao tipo
            ];
        });

        return $groupedProjects;
    }

    private function globalSidebarOrders(): array
    {
        $admin = self::where('role', 'Administrador')->orderBy('id')->first();

        if (!$admin) {
            return [[], []];
        }

        $groupType = 'sidebarGroupOrderGlobal';
        $projectType = 'sidebarProjectsOrderGlobal';

        $hasGlobalGroup = UserPreferrence::where('type', $groupType)->exists();
        $hasGlobalProject = UserPreferrence::where('type', $projectType)->exists();

        // Backfill único: se global ainda não existir, copia a ordem antiga do admin.
        if (!$hasGlobalGroup) {
            $legacyGroup = UserPreferrence::where('created_by', $admin->id)
                ->where('type', 'sidebarGroupOrder')
                ->pluck('value')
                ->toArray();
            foreach ($legacyGroup as $value) {
                UserPreferrence::create([
                    'type' => $groupType,
                    'value' => $value,
                    'created_by' => $admin->id,
                ]);
            }
        }

        if (!$hasGlobalProject) {
            $legacyProject = UserPreferrence::where('created_by', $admin->id)
                ->where('type', 'sidebarProjectsOrder')
                ->pluck('value')
                ->toArray();
            foreach ($legacyProject as $value) {
                UserPreferrence::create([
                    'type' => $projectType,
                    'value' => $value,
                    'created_by' => $admin->id,
                ]);
            }
        }

        $orderGroupSidebar = UserPreferrence::where('type', $groupType)
            ->pluck('value')
            ->toArray();

        $orderProjectSidebar = UserPreferrence::where('type', $projectType)
            ->pluck('value')
            ->toArray();

        return [$orderGroupSidebar, $orderProjectSidebar];
    }


}
