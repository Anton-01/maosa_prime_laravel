<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_session_id',
        'page_visit_id',
        'activity_type',
        'activity_description',
        'element_id',
        'element_class',
        'element_text',
        'url',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'user_session_id');
    }

    public function pageVisit(): BelongsTo
    {
        return $this->belongsTo(PageVisit::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public static function log(
        string $activityType,
        ?string $description = null,
        ?array $metadata = null,
        ?int $userId = null,
        ?int $sessionId = null,
        ?int $pageVisitId = null
    ): self {
        return static::create([
            'user_id' => $userId ?? auth()->id(),
            'user_session_id' => $sessionId ?? session('user_session_id'),
            'page_visit_id' => $pageVisitId,
            'activity_type' => $activityType,
            'activity_description' => $description,
            'url' => request()->fullUrl(),
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
