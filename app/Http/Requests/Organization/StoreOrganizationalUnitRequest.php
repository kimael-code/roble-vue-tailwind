<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationalUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create new organizational units');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'numeric', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:10',],
            'acronym' => ['nullable', 'string', 'max:20'],
            'floor' => ['nullable', 'string', 'max:5'],
            'organizational_unit_id' => ['nullable', 'numeric', 'integer', 'exists:organizational_units,id'],
        ];
    }
}
