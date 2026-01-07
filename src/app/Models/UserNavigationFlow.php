<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNavigationFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_session_id',
        'from_page_visit_id',
        'to_page_visit_id',
        'from_url',
        'to_url',
        'transition_time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'user_session_id');
    }

    public function fromPageVisit(): BelongsTo
    {
        return $this->belongsTo(PageVisit::class, 'from_page_visit_id');
    }

    public function toPageVisit(): BelongsTo
    {
        return $this->belongsTo(PageVisit::class, 'to_page_visit_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
