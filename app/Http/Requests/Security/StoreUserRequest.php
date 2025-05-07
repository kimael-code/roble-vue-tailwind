<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255',],
            'email'       => ['required', 'email', 'max:255', 'unique:users',],
            'is_external' => ['required', 'boolean',],
            'id_card'     => ['required_if_accepted:is_external', 'nullable', 'string', 'max:8', 'unique:people'],
            'names'       => ['required_if_accepted:is_external', 'nullable', 'string', 'max:255',],
            'surnames'    => ['required_if_accepted:is_external', 'nullable', 'string', 'max:255',],
            'phones'      => ['nullable', 'string', 'regex:/\+?\(?[\d\s\-\.]{7,}\d/'],
            'emails'      => ['nullable', 'string', 'regex:/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/'],
            'position'    => ['nullable', 'string', 'max:255'],
            'staff_type'  => ['nullable', 'string', 'max:255'],
            'ou_names'    => ['nullable', 'array',],
            'roles'       => ['nullable', 'array',],
            'permissions' => ['nullable', 'array',],
        ];
    }
}
