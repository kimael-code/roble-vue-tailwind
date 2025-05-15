<?php

namespace App\Policies;

use App\Models\Organization\OrganizationalUnit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationalUnitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read any organizational unit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrganizationalUnit $organizationalUnit): bool
    {
        return $user->can('read organizational unit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create organizational units');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrganizationalUnit $organizationalUnit): bool
    {
        return $user->can('update organizational units');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrganizationalUnit $organizationalUnit): Response|bool
    {
        if ($organizationalUnit->users->isNotEmpty())
        {
            return Response::deny(__('There are users who belong to this administrative unit'));
        }

        return $user->can('delete organizational units');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrganizationalUnit $organizationalUnit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrganizationalUnit $organizationalUnit): bool
    {
        return false;
    }
}
