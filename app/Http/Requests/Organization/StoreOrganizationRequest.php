<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create new organizations');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rif' => [
                'required',
                'string',
                'max:12',
                'uppercase',
                'unique:organizations',
                'regex:/[JGVEPC]-[0-9]{8}-[0-9]{1}/',
            ],
            'name' => ['required', 'string', 'max:255',],
            'acronym' => ['nullable', 'string', 'max:20',],
            'address' => ['nullable', 'string', 'max:2000'],
            'logo_path' => [
                'image',
                'mimes:png',
                'max:512',
            ],
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
            'rif' => 'RIF',
            'name' => 'Nombre',
            'acronym' => 'Acrónimo',
            'address' => 'Dirección',
            'logo_path' => 'Logo',
        ];
    }
}
