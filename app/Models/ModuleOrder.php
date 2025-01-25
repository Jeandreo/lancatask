<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleOrder extends Model
{
    protected $table = 'modules_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order',
        'user_id',
        'module_id',
        'project_id',
    ];
}
