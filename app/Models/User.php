<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\MvpRole;
use Database\Factories\UserFactory;
use HasinHayder\Tyro\Concerns\HasTyroRoles;
use HasinHayder\TyroLogin\Traits\HasTwoFactorAuth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasTyroRoles, HasTwoFactorAuth, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'parent_id',
        'mvp_role',
        'account_status',
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
            'mvp_role' => MvpRole::class,
            'account_status' => AccountStatus::class,
            'suspended_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function isSuperAdmin(): bool
    {
        if ($this->mvp_role === MvpRole::SuperAdmin) {
            return true;
        }

        return $this->tyroRoleSlugs()->contains('super-admin');
    }

    public function isReseller(): bool
    {
        return $this->mvp_role === MvpRole::Reseller;
    }

    public function isSubReseller(): bool
    {
        return $this->mvp_role === MvpRole::SubReseller;
    }

    public function isClient(): bool
    {
        return $this->mvp_role === MvpRole::Client;
    }
}
