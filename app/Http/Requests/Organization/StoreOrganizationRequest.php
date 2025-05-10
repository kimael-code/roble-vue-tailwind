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
        return $this->user()->can('create organizations');
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
                ...$this->isPrecognitive() ? [] : ['required'],
                'image',
                'mimes:png',
                'max:512',
            ],
        ];
    }
}
