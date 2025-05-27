<?php

namespace App\Policies;

use App\Models\Security\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read any permission');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->can('read permission');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create new permissions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->can('update permissions');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): Response
    {
        return $user->can('delete permissions')
            && $permission->roles->isEmpty()
            && $permission->users->isEmpty()
            ? Response::allow()
            : Response::deny('There are roles or users having this permission.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): Response
    {
        return $user->can('delete permissions')
            && $permission->roles->isEmpty()
            && $permission->users->isEmpty()
            ? Response::allow()
            : Response::deny('There are roles or users having this permission.');
    }
}
