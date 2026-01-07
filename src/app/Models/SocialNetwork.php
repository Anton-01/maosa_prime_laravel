<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'status',
        'order'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get the listings that use this social network
     */
    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'listing_social_links')
            ->withPivot('url', 'order')
            ->withTimestamps();
    }

    /**
     * Scope query to only include active social networks
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope query to order by order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
