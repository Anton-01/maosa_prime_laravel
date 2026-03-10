<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AmenityStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'boolean']
        ];
    }
}
