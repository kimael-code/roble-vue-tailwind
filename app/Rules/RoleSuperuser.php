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
        if (in_array(__('Superuser'), $value) && !auth()->user()->hasRole(1))
        {
            $fail('The superuser role can only be assigned by another superuser.')->translate();
        }
    }
}
