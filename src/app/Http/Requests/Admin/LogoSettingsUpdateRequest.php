<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LogoSettingsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'max:3000'],
            'favicon' => ['nullable', 'image', 'max:3000'],
            'old_logo' => ['nullable', 'string', 'max:255'],
            'old_favicon' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'logo' => 'logo',
            'favicon' => 'favicon',
        ];
    }
}
