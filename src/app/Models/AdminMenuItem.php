<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Menu item stored by the legacy menu-builder package. parent_id = 0
 * means top level; depth mirrors the nesting level.
 */
class AdminMenuItem extends Model
{
    protected $fillable = ['label', 'link', 'parent_id', 'sort', 'class', 'menu_id', 'depth'];

    protected $casts = [
        'parent_id' => 'integer',
        'sort' => 'integer',
        'menu_id' => 'integer',
        'depth' => 'integer',
    ];

    public function getTable(): string
    {
        return config('menu.table_prefix') . config('menu.table_name_items');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(AdminMenu::class, 'menu_id');
    }
}
