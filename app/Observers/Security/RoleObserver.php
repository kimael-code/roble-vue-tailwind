<?php

namespace App\Observers\Security;

use App\Models\Security\Role;
use App\Models\User;
use App\Notifications\ActionHandledOnModel;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        session()->flash('message', [
            'message' => "{$role->name} ({$role->description})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('create new roles')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $role->id,
                    'type' => __('role'),
                    'name' => "{$role->name}",
                    'timestamp' => $role->created_at,
                ],
                'created',
                ['routeName' => 'roles', 'routeParam' => 'role']
            ));
        }
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        session()->flash('message', [
            'message' => "{$role->name} ({$role->description})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('update roles')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $role->id,
                    'type' => __('role'),
                    'name' => "{$role->name}",
                    'timestamp' => $role->updated_at,
                ],
                'updated',
                ['routeName' => 'roles', 'routeParam' => 'role']
            ));
        }
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        session()->flash('message', [
            'message' => "{$role->name} ({$role->description})",
            'title' => __('DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete roles')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'type' => __('role'),
                    'name' => "{$role->name}",
                    'timestamp' => now()->toIso8601String(),
                ],
                'deleted',
                ['routeName' => 'roles', 'routeParam' => 'role']
            ));
        }
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        //
    }
}
