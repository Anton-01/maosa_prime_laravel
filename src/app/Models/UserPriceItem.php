<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPriceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_price_list_id',
        'fuel_terminal_id',
        'terminal_name',
        'magna_price',
        'premium_price',
        'diesel_price',
        'sort_order',
    ];

    protected $casts = [
        'magna_price' => 'decimal:4',
        'premium_price' => 'decimal:4',
        'diesel_price' => 'decimal:4',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(UserPriceList::class, 'user_price_list_id');
    }

    public function fuelTerminal(): BelongsTo
    {
        return $this->belongsTo(FuelTerminal::class);
    }

    public function getFormattedMagnaPriceAttribute(): string
    {
        return $this->magna_price ? number_format($this->magna_price, 4) : '-';
    }

    public function getFormattedPremiumPriceAttribute(): string
    {
        return $this->premium_price ? number_format($this->premium_price, 4) : '-';
    }

    public function getFormattedDieselPriceAttribute(): string
    {
        return $this->diesel_price ? number_format($this->diesel_price, 4) : '-';
    }
}
