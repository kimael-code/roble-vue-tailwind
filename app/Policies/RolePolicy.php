<?php

namespace App\Policies;

use App\Models\Security\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool|null
    {
        return $user->can('read any role') ? true : null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool|null
    {
        return $user->can('read role') ? true : null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool|null
    {
        return $user->can('create new roles') ? true : null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool|null
    {
        return $user->can('update roles') ? true : null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): Response|bool|null
    {
        if ($role->id === 0 || $role->name === __('Superuser'))
        {
            return Response::deny(__('The Superuser role cannot be deleted.'));
        }

        if ($role->permissions->isNotEmpty())
        {
            return Response::deny(__('There are permissions having this role.'));
        }

        if ($role->users->isNotEmpty())
        {
            return Response::deny(__('There are users having this role.'));
        }

        return $user->can('delete roles') ? true : null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
