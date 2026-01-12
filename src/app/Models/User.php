<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'can_view_price_table', 'is_approved', 'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', 'password' => 'hashed', 'can_view_price_table' => 'boolean',
    ];

    /**
     * Get the price lists for the user.
     */
    public function priceLists(): HasMany
    {
        return $this->hasMany(UserPriceList::class);
    }

    /**
     * Get the active price list for the user.
     */
    public function activePriceList(): HasOne
    {
        return $this->hasOne(UserPriceList::class)
            ->where('is_active', true)
            ->orderByDesc('price_date');
    }

    /**
     * Get the price legends for the user.
     */
    public function priceLegends(): HasMany
    {
        return $this->hasMany(UserPriceLegend::class)->orderBy('sort_order');
    }

    /**
     * Get the sessions for the user.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the page visits for the user.
     */
    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }

    /**
     * Get the activities for the user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the navigation flows for the user.
     */
    public function navigationFlows(): HasMany
    {
        return $this->hasMany(UserNavigationFlow::class);
    }

    /**
     * Check if user can view price table.
     */
    public function canViewPriceTable(): bool
    {
        return (bool) $this->can_view_price_table;
    }
}
