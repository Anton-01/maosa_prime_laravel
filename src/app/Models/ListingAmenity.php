<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Amenity create(array $attributes = [])
 */
class ListingAmenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id', 'amenity_id'
    ];

    function amenity() : BelongsTo {
        return $this->belongsTo(Amenity::class, 'amenity_id', 'id');
    }
}
