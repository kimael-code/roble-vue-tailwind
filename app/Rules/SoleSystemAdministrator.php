<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SoleSystemAdministrator implements ValidationRule
{
    public function __construct(
        public User $user,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sysadminsCount = User::with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', 'Administrador de Sistemas')->toArray()
        )->count();

        if (
            $sysadminsCount === 1
            && $this->user->hasRole('Administrador de Sistemas')
            && !in_array('Administrador de Sistemas', $value, true)
        )
        {
            $fail('This is the only user with System Administrator permissions.')->translate();
        }
    }
}
