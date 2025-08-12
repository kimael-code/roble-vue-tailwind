<?php

namespace App\Http\Requests\Security;

use App\Rules\SoleSystemAdministrator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255',],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user),],
            'is_external' => ['required', 'boolean',],
            'id_card' => ['required_if_accepted:is_external', 'nullable', 'string', 'max:8', 'regex:/^[0-9]*$/' ,Rule::unique('people')->ignore($this->input('id_card'), 'id_card'),],
            'names' => ['required_if_accepted:is_external', 'nullable', 'string', 'max:255',],
            'surnames' => ['required_if_accepted:is_external', 'nullable', 'string', 'max:255',],
            'phones' => ['nullable', 'string', 'regex:/\+?\(?[\d\s\-\.]{7,}\d/'],
            'emails' => ['nullable', 'string', 'regex:/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/'],
            'position' => ['nullable', 'string', 'max:255'],
            'staff_type' => ['nullable', 'string', 'max:255'],
            'ou_names' => ['nullable', 'array',],
            'roles' => ['nullable', 'array', new SoleSystemAdministrator($this->user)],
            'permissions' => ['nullable', 'array',],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nombre',
            'email' => 'Correo Electrónico',
            'is_external' => 'Usuario Externo',
            'id_card' => 'Nro. de CI',
            'names' => 'Nombres',
            'surnames' => 'Apellidos',
            'phones' => 'Teléfonos',
            'emails' => 'Correos Electrónicos',
            'position' => 'Cargo',
            'staff_type' => 'Personal',
            'ou_names' => 'Unidad Administrativa',
            'roles' => 'Roles',
            'permissions' => 'Permisos Directos',
        ];
    }
}
