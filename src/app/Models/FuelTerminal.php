<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name . ($this->code ? " - {$this->code}" : ""),
        );
    }
    public function priceItems(): HasMany
    {
        return $this->hasMany(UserPriceItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
