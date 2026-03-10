<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BlogCreateRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'image'],
            'title' => ['required', 'max:255', 'unique:blogs,title'],
            'category' => ['required', 'integer'],
            'description' => ['required'],
            'is_popular' => ['required', 'boolean'],
            'status' => ['required', 'boolean']
        ];
    }
}
