<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialCategory extends Model
{
    protected $table = 'financial_categories';

    protected $fillable = [
        'name',
        'type',
        'status',
        'filed_by',
        'created_by',
        'updated_by',
    ];
}
