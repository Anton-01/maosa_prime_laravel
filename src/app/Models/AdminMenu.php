<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menu header stored by the legacy menu-builder package. The public site
 * renders these menus, so the table layout must stay compatible.
 */
class AdminMenu extends Model
{
    protected $fillable = ['name'];

    public function getTable(): string
    {
        return config('menu.table_prefix') . config('menu.table_name_menus');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AdminMenuItem::class, 'menu_id');
    }
}
