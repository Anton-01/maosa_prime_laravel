<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_session_id',
        'url',
        'route_name',
        'page_title',
        'referrer',
        'time_on_page',
        'ip_address',
        'visited_at',
        'left_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'user_session_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getFormattedTimeOnPageAttribute(): string
    {
        if (!$this->time_on_page) {
            return '-';
        }

        $minutes = floor($this->time_on_page / 60);
        $seconds = $this->time_on_page % 60;

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }
        return sprintf('%ds', $seconds);
    }
}
