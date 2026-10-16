<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Une permission peut être attribuée à plusieurs rôles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class, 
            'role_permissions', 
            'permission_id', 
            'role_id'
        );
    }
}