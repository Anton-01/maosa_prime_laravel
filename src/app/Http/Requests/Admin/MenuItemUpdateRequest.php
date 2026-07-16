<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MenuItemUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $itemsTable = config('menu.table_prefix') . config('menu.table_name_items');

        return [
            'label' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', "exists:{$itemsTable},id"],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'label' => 'etiqueta',
            'link' => 'enlace',
            'class' => 'clase CSS',
            'parent_id' => 'elemento padre',
        ];
    }
}
