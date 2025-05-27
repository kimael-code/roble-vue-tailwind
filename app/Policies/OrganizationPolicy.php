<?php

namespace App\Policies;

use App\Models\Organization\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read any organization');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        $userBelongsToOrganization = false;

        foreach ($organization->organizationalUnits as $ou)
        {
            if ($user->activeOrganizationalUnits->contains($ou))
            {
                $userBelongsToOrganization = true;
            }
        }

        return $user->can('read any organization')
            || ($user->can('read organization') && $userBelongsToOrganization);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create new organizations');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->can('update organizations');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): Response | bool
    {
        $ThereIsOtherActiveOrganization = (bool)Organization::where('id', '<>', $organization->id)->active()->count();

        if (!$ThereIsOtherActiveOrganization)
        {
            return Response::deny('You cannot delete the only active Organization');
        }

        return $user->can('delete organizations')
            && $organization->organizationalUnits->isEmpty()
            ? Response::allow()
            : Response::deny(__('The organization has associated organizational units.'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }
}
