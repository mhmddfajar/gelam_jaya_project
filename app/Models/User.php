<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'resident_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Resident associated with this user (if role is warga).
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function isWarga(): bool
    {
        return $this->role === Role::WARGA;
    }

    public function isStaf(): bool
    {
        return $this->role === Role::STAF;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    /**
     * Get default redirect route for authenticated user based on role.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            Role::ADMIN => route('admin.dashboard'),
            Role::STAF => route('staf.residents.index'),
            Role::WARGA => route('warga.profile.edit'),
            default => route('home'),
        };
    }
}
