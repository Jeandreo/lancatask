<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agendas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'general',
        'date_start',
        'hour_start',
        'date_end',
        'hour_end',
        'recurrent',
        'frequency',
        'week_days',
        'duration',
        'status',
        'color',
        'created_by',
    ];

    /**
     * Get the infos associated with the user.
    */
    public function usersParticipants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agenda_users', 'agenda_id', 'user_id');
    }
}
