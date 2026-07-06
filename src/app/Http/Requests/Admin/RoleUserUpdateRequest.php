<?php

namespace App\Http\Requests\Admin;

use App\Models\CatUsuarioImportado;
use Illuminate\Foundation\Http\FormRequest;

class RoleUserUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users,email,' . $this->route('role_user')],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'role' => ['required'],
            'can_view_price_table' => ['nullable', 'boolean'],
            'id_estacion' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!CatUsuarioImportado::esEstacionActiva((int) $value)) {
                        $fail('La estación seleccionada no existe o no está activa.');
                    }
                },
            ],
        ];
    }
}
