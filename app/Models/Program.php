<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'establishment_id',
        'department_id',
        'name',
        'code',
        'degree_type',
        'duration_years',
        'grading_system',
    ];
}
