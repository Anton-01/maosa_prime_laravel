<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'social_network_id',
        'url',
        'order'
    ];

    /**
     * Get the listing that owns the social link
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get the social network
     */
    public function socialNetwork()
    {
        return $this->belongsTo(SocialNetwork::class);
    }
}
