<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OurFeatureUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'icon' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'icon' => 'icono',
            'title' => 'título',
            'short_description' => 'descripción corta',
            'status' => 'estatus',
        ];
    }
}
