<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaMember extends Model
{
    use HasFactory;
    protected $table = 'agendas_member';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'member_id',
        'agenda_id',
        'status',
    ];

    function information()
    {
        if($this->type == 'user'){
            return $this->belongsTo(User::class, 'member_id');
        } else {
            return $this->belongsTo(Client::class, 'member_id');
        }
    }
}
