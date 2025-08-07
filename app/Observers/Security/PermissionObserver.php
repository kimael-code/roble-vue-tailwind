<?php

namespace App\Observers\Security;

use App\Models\Security\Permission;
use App\Models\User;
use App\Notifications\ActionHandledOnModel;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        session()->flash('message', [
            'message' => "{$permission->name} ({$permission->description})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('create new permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $permission->id,
                    'type' => __('permission'),
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => $permission->created_at,
                ],
                'created',
                ['routeName' => 'permissions', 'routeParam' => 'permission']
            ));
        }
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission): void
    {
        session()->flash('message', [
            'message' => "{$permission->name} ({$permission->description})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('update permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $permission->id,
                    'type' => __('permission'),
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => $permission->updated_at,
                ],
                'updated',
                ['routeName' => 'permissions', 'routeParam' => 'permission']
            ));
        }
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission): void
    {
        session()->flash('message', [
            'message' => "{$permission->name} ({$permission->description})",
            'title' => __('DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'type' => __('permission'),
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => now(),
                ],
                'deleted',
                ['routeName' => 'permissions', 'routeParam' => 'permission']
            ));
        }
    }

    /**
     * Handle the Permission "restored" event.
     */
    public function restored(Permission $permission): void
    {
        //
    }

    /**
     * Handle the Permission "force deleted" event.
     */
    public function forceDeleted(Permission $permission): void
    {
        //
    }
}
