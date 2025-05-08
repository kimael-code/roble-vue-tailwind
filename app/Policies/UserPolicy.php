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
        return $user->can('create users');
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
        $sysadminsCount = User::with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', 'Administrador de Sistemas')->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole('Administrador de Sistemas'))
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
        $sysadminsCount = User::with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', 'Administrador de Sistemas')->toArray()
        )->count();

        if ($sysadminsCount === 1 && $model->hasRole('Administrador de Sistemas'))
        {
            return Response::deny(__('This is the only existing System Administrator, therefore it cannot be deleted.'));
        }

        if ($user->is($model))
        {
            return Response::deny(__('You cannot delete yourself.'));
        }

        return $user->can('delete users');
    }
}
