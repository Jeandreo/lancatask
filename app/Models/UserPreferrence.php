<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferrence extends Model
{
    protected $table = 'user_preferrences';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'name',
        'value',
        'created_by',
    ];
}
