<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    use HasRoles;

    public $guard_name = 'web';

    /**
     * Assign a default role when a user is created if they have no role.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            try {
                if ($user->roles->isEmpty()) {
                    $role = Role::firstOrCreate(['name' => 'Client', 'guard_name' => 'web']);
                    $user->assignRole($role);
                }
            } catch (\Exception $e) {
                // Roles table might not exist yet during migrations
                \Log::warning('Could not assign default role: ' . $e->getMessage());
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'active',
        'wallet_balance',
        'stripe_customer_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'stripe_customer_id'
    ];

    /**
     * Relationship: user belongs to a role.
     */
    /* USE THIS WHEN ROLES ARE IMPLEMENTED
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    */
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
            'active' => 'boolean',
            'wallet_balance' => 'integer',
        ];
    }

    /**
     * Get the user's wallet transactions.
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get formatted balance (e.g. 10.50 €)
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->wallet_balance / 100, 2, ',', '.') . ' €';
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function trips()
    {
        return $this->hasManyThrough(Trip::class, Reservation::class);
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
