<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuelTerminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function priceItems(): HasMany
    {
        return $this->hasMany(UserPriceItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
