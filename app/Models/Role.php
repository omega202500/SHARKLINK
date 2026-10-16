<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public $timestamps = false; // La table n'a pas created_at/updated_at

    protected $fillable = [
        'name',
        'code',
        'scope',
        'description',
    ];

     /**
     * Un rôle peut être attribué à plusieurs memberships
     */
    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(
            EstablishmentMembership::class, 
            'membership_roles', 
            'role_id', 
            'membership_id'
        )->withPivot('assigned_by', 'assigned_at');
    }
    /**
     * Un rôle a plusieurs permissions
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class, 
            'role_permissions', 
            'role_id', 
            'permission_id'
        );
    }

    /**
     * Vérifier si le rôle a une permission spécifique
     */
    public function hasPermission(string $permissionCode): bool
    {
        return $this->permissions()->where('code', $permissionCode)->exists();
    }
}