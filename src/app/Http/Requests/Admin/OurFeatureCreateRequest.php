<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OurFeatureCreateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'icon' => ['required'],
            'title' => ['required', 'max:255'],
            'short_description' => ['required', 'max:255'],
            'status' => ['required', 'boolean']
        ];
    }
}
