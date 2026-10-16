<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EstablishmentMembership extends Model
{
    public $timestamps = false; // La table n'a pas created_at/updated_at

    protected $fillable = [
        'establishment_id',
        'user_id',
        'member_number',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    /**
     * Une appartenance appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une appartenance appartient à un établissement
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    /**
     * Une appartenance a plusieurs rôles (via membership_roles)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'membership_roles',
            'membership_id',
            'role_id'
        )->withPivot('assigned_by', 'assigned_at');
        // ⚠️ Ne pas ajouter ->withTimestamps() ici car la table n'a pas created_at/updated_at
    }

    /**
     * Une appartenance peut être un profil étudiant
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'membership_id');
    }

    /**
     * Une appartenance peut être un profil employé
     */
    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class, 'membership_id');
    }

    /**
     * Vérifier si ce membership a un rôle spécifique
     */
    public function hasRole(string $roleCode): bool
    {
        return $this->roles()->where('code', $roleCode)->exists();
    }

    /**
     * Filtrer les memberships actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIF');
    }
}
