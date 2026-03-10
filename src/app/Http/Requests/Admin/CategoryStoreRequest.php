<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'image_icon' => ['required', 'image', 'max:3000'],
            'background_image' => ['required', 'image', 'max:3000'],
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'show_at_home' => ['required', 'boolean'],
            'status' => ['required', 'boolean']
        ];
    }
}
