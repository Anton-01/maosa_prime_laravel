<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'country',
        'city',
        'region',
        'latitude',
        'longitude',
        'started_at',
        'ended_at',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function navigationFlows(): HasMany
    {
        return $this->hasMany(UserNavigationFlow::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getDurationAttribute(): ?int
    {
        if (!$this->ended_at) {
            return null;
        }
        return $this->started_at->diffInSeconds($this->ended_at);
    }

    public function getFormattedDurationAttribute(): string
    {
        $duration = $this->duration;
        if ($duration === null) {
            return 'En curso';
        }

        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        }
        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }
        return sprintf('%ds', $seconds);
    }
}
