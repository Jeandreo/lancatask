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
        'id_google',
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

    function membersUsers()
    {
        return $this->hasMany(AgendaMember::class, 'agenda_id', 'id')->where('type', 'user')->with('user');
    }

    function membersClients()
    {
        return $this->hasMany(AgendaMember::class, 'agenda_id', 'id')->where('type', 'client')->with('client');
    }
}
