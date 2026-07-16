<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_email' => ['required', 'string', 'max:255', 'email'],
            'site_phone' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'site_name' => 'nombre del sitio',
            'site_email' => 'email del sitio',
            'site_phone' => 'teléfono del sitio',
        ];
    }
}
