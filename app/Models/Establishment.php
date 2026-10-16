<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establishment extends Model
{
    // Désactiver les timestamps automatiques si ta table n'a pas created_at/updated_at
    // (d'après ton SQL, elle les a, donc on les garde)
    public $timestamps = true;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'logo_path',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Un établissement a plusieurs appartenances (memberships)
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(EstablishmentMembership::class);
    }

    /**
     * Un établissement a plusieurs années académiques
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * Un établissement a plusieurs facultés
     */
    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    /**
     * Un établissement a plusieurs départements
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Un établissement a plusieurs programmes
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Un établissement a plusieurs cours
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Un établissement a plusieurs campus
     */
    public function campuses(): HasMany
    {
        return $this->hasMany(Campus::class);
    }

    /**
     * Filtrer les établissements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIF');
    }
}