<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read any user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('read user');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create new users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('update users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool|Response
    {
        $sysAdminRole = __('Systems Administrator');
        $sysadminsCount = User::withTrashed()->with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', $sysAdminRole)->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole($sysAdminRole))
        {
            return Response::deny(__('This is the only existing System Administrator, therefore it cannot be deleted.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot delete yourself.'));
        }

        return $user->can('delete users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('restore users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool|Response
    {
        $sysAdminRole = __('Systems Administrator');
        $sysadminsCount = User::withTrashed()->with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', $sysAdminRole)->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole($sysAdminRole))
        {
            return Response::deny(__('This is the only existing System Administrator, therefore it cannot be deleted.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot delete yourself.'));
        }

        return $user->can('force delete users');
    }

    /**
     * Determine whether the user can enable the model.
     */
    public function enable(User $user, User $model): bool
    {
        return $user->can('activate users');
    }

    /**
     * Determine whether the user can disable the model.
     */
    public function disable(User $user, User $model): bool|Response
    {
        $sysAdminRole = __('Systems Administrator');
        $sysadminsCount = User::with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', $sysAdminRole)->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole($sysAdminRole))
        {
            return Response::deny(__('This is the only existing System Administrator, therefore it cannot be deactivated.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot deactivate yourself.'));
        }

        return $user->can('deactivate users');
    }
}
