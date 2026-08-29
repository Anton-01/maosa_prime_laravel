<?php

namespace App\Http\Requests\Admin;

use App\Models\CatUsuarioImportado;
use App\Models\EstacionNacional;
use Illuminate\Foundation\Http\FormRequest;

class RoleUserCreateRequest extends FormRequest
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
            'email' => ['required', 'max:255', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required'],
            'can_view_price_table' => ['nullable', 'boolean'],
            'permiso_precios_pemex' => ['nullable', 'boolean'],
            'estaciones_asignadas' => [
                'array',
                'required_if_accepted:permiso_precios_pemex',
            ],
            'estaciones_asignadas.*' => [
                'integer',
                function ($attribute, $value, $fail) {
                    if (! EstacionNacional::esActiva((int) $value)) {
                        $fail('La estación seleccionada no existe o no está activa.');
                    }
                },
            ],
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

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'estaciones_asignadas.required_if_accepted' => 'Debe asignar al menos una estación cuando el permiso de Precios PEMEX está activo.',
        ];
    }
}
