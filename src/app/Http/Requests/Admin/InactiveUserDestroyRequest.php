<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InactiveUserDestroyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            // El administrador debe confirmar con su contraseña actual
            'admin_password' => ['required', 'current_password:web'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => 'Debe seleccionar al menos un usuario.',
            'user_ids.min' => 'Debe seleccionar al menos un usuario.',
            'admin_password.required' => 'Debe ingresar su contraseña actual para confirmar.',
            'admin_password.current_password' => 'La contraseña ingresada no es correcta.',
        ];
    }
}
