<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RoleSuperuser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        dd($value, !auth()->user()->hasRole(__('Superuser')));
        if (in_array(__('Superuser'), $value) && !auth()->user()->hasRole(__('Superuser')))
        {
            $fail('This is the only user with Systems Administrator permissions.')->translate();
        }
    }
}
