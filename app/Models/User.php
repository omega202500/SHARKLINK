<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Les attributs qui sont mass assignable.
     */
    protected $fillable = [
        'public_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar_path',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être convertis (cast).
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Accesseur pour obtenir le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Un utilisateur a plusieurs appartenances à des établissements
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(EstablishmentMembership::class);
    }

    /**
     * Un utilisateur a plusieurs établissements (via memberships)
     */
    public function establishments(): BelongsToMany
    {
        return $this->belongsToMany(
            Establishment::class,
            'establishment_memberships',
            'user_id',
            'establishment_id'
        )->withPivot('id', 'member_number', 'status', 'joined_at');
    }

    /**
     * ✅ Obtenir tous les rôles d'un utilisateur dans un établissement spécifique
     * (C'était cette méthode qui était commentée par erreur !)
     */
    public function rolesInEstablishment(int $establishmentId): Collection
    {
        $membership = $this->memberships()
            ->where('establishment_id', $establishmentId)
            ->first();

        if (! $membership) {
            return new Collection;
        }

        return $membership->roles;
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique dans un établissement
     */
    public function hasRoleInEstablishment(string $roleCode, int $establishmentId): bool
    {
        return $this->rolesInEstablishment($establishmentId)
            ->contains('code', $roleCode);
    }

    /**
     * Vérifier si l'utilisateur est étudiant dans au moins un établissement
     */
    public function isStudent(): bool
    {
        return $this->memberships()->whereHas('studentProfile')->exists();
    }

    /**
     * Vérifier si l'utilisateur est employé dans au moins un établissement
     */
    public function isStaff(): bool
    {
        return $this->memberships()->whereHas('staffProfile')->exists();
    }
}
