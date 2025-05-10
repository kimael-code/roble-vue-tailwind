<?php

namespace App\Http\Requests\Organization;

use App\Rules\DeactivatableOrganization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update organizations');
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
                Rule::unique('organizations')->ignore($this->organization),
                'regex:/[JGVEPC]-[0-9]{8}-[0-9]{1}/',
            ],
            'name' => ['required', 'string', 'max:255',],
            'acronym' => ['nullable', 'string', 'max:20',],
            'address' => ['nullable', 'string', 'max:2000'],
            'logo_path' => [
                ...$this->isPrecognitive() ? [] : ['required'],
                ...Storage::disk('public')->exists($this->logo_path ?? '') ? [] : ['image'],
                ...Storage::disk('public')->exists($this->logo_path ?? '') ? [] : ['mimes:png'],
                ...Storage::disk('public')->exists($this->logo_path ?? '') ? [] : ['max:512'],
            ],
            'disabled' => ['nullable', 'boolean', new DeactivatableOrganization($this->organization),]
        ];
    }
}
