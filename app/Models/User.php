<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Indiquer à Laravel que la clé primaire n'est pas incrémentée automatiquement par Laravel 
    // (car gérée par la séquence PostgreSQL, bien que Laravel le gère souvent bien, c'est plus sûr)
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Les attributs qui sont mass assignable.
     * Basé sur ta table 'users' du fichier SQL.
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
        'remember_token', // Ajouté pour la compatibilité avec l'auth Laravel
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
     * Accesseur pour obtenir le nom complet (optionnel mais très utile)
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}