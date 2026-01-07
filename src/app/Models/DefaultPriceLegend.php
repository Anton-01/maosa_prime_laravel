<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultPriceLegend extends Model
{
    use HasFactory;

    protected $fillable = [
        'legend_text',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function copyToUser(User $user): void
    {
        $defaults = static::active()->orderBy('sort_order')->get();

        foreach ($defaults as $default) {
            UserPriceLegend::create([
                'user_id' => $user->id,
                'legend_text' => $default->legend_text,
                'sort_order' => $default->sort_order,
                'is_active' => true,
            ]);
        }
    }
}
