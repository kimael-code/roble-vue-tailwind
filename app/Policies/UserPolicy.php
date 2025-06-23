<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool|null
    {
        return $user->can('read any user') ? true : null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool|null
    {
        return $user->can('read user') ? true : null;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool|null
    {
        return $user->can('create new users') ? true : null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool|null
    {
        return $user->can('update users') ? true : null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response|bool|null
    {
        $sysAdminRole = __('Systems Administrator');
        $sysadminsCount = User::withTrashed()->with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', $sysAdminRole)->toArray()
        )->count();

        $superuserRole = __('Superuser');
        $superusersCount = User::withTrashed()->with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', $superuserRole)->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole($sysAdminRole) && $superusersCount === 0)
        {
            return Response::deny(__('This is the only user with Systems Administrator permissions.'));
        }

        if ($superusersCount === 1 && $model->hasRole($superuserRole) && $sysadminsCount === 0)
        {
            return Response::deny(__('This is the only user with Superuser permissions.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot delete yourself.'));
        }

        return $user->can('delete users') ? true : null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('restore users') ? true : null;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response|bool|null
    {
        $sysAdminRole = __('Systems Administrator');
        $sysadminsCount = User::withTrashed()->with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', $sysAdminRole)->toArray()
        )->count();

        $superuserRole = __('Superuser');
        $superusersCount = User::withTrashed()->with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', $superuserRole)->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole($sysAdminRole) && $superusersCount === 0)
        {
            return Response::deny(__('This is the only user with Systems Administrator permissions.'));
        }

        if ($superusersCount === 1 && $model->hasRole($superuserRole) && $sysadminsCount === 0)
        {
            return Response::deny(__('This is the only user with Superuser permissions.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot delete yourself.'));
        }

        return $user->can('force delete users') ? true : null;
    }

    /**
     * Determine whether the user can enable the model.
     */
    public function enable(User $user, User $model): bool
    {
        return $user->can('activate users') ? true : null;
    }

    /**
     * Determine whether the user can disable the model.
     */
    public function disable(User $user, User $model): Response|bool|null
    {
        $sysAdminRole = __('Systems Administrator');
        $sysadminsCount = User::with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', $sysAdminRole)->toArray()
        )->count();

        $superuserRole = __('Superuser');
        $superusersCount = User::withTrashed()->with('roles')->get()->filter(
            fn($user) => $user->roles->where('name', $superuserRole)->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole($sysAdminRole) && $superusersCount === 0)
        {
            return Response::deny(__('This is the only user with Systems Administrator permissions.'));
        }

        if ($superusersCount === 1 && $model->hasRole($superuserRole) && $sysadminsCount === 0)
        {
            return Response::deny(__('This is the only user with Superuser permissions.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot deactivate yourself.'));
        }

        return $user->can('deactivate users') ? true : null;
    }
}
