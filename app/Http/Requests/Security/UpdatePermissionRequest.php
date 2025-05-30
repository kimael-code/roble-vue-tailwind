<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update permissions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => [
                'required',
                'string',
                'lowercase',
                'max:255',
                Rule::unique('permissions')->ignore($this->permission),
            ],
            'description' => ['required', 'string', 'lowercase', 'max:255',],
            'guard_name'  => ['required', 'string', 'lowercase' , 'regex:/^(web)$/',],
            'set_menu'    => ['nullable', 'boolean',],
        ];
    }
}
