<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'birth_place',
        'gender',
        'previous_school',
        'previous_diploma',
        'graduation_year',
        'program_id',
        'level_id',
        'status',
    ];
}
