<?php

namespace App\Observers\Security;

use App\Models\Security\Permission;
use App\Models\User;
use App\Notifications\Security\PermissionNotification;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        session()->flash('msg', [
            'msg'   => "{$permission->name} ({$permission->description})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('create permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new PermissionNotification(
                auth()->user(),
                [
                    'id' => $permission->id,
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => $permission->created_at,
                ],
                'created'
            ));
        }
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission): void
    {
        session()->flash('msg', [
            'msg'   => "{$permission->name} ({$permission->description})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('update permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new PermissionNotification(
                auth()->user(),
                [
                    'id' => $permission->id,
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => $permission->created_at,
                ],
                'updated'
            ));
        }
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission): void
    {
        session()->flash('msg', [
            'msg'   => "{$permission->name} ({$permission->description})",
            'title' => __('DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new PermissionNotification(
                auth()->user(),
                [
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => now()->toISOString(),
                ],
                'deleted'
            ));
        }
    }

    /**
     * Handle the Permission "restored" event.
     */
    public function restored(Permission $permission): void
    {
        session()->flash('msg', [
            'msg'   => "{$permission->name} ({$permission->description})",
            'title' => __('RESTORED'),
            'type'  => 'success',
        ]);

        $users = User::permission('restore permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new PermissionNotification(
                auth()->user(),
                [
                    'id' => $permission->id,
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => now()->toISOString(),
                ],
                'restored'
            ));
        }
    }

    /**
     * Handle the Permission "force deleted" event.
     */
    public function forceDeleted(Permission $permission): void
    {
        session()->flash('msg', [
            'msg'   => "{$permission->name} ({$permission->description})",
            'title' => __('HARD DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete permissions')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new PermissionNotification(
                auth()->user(),
                [
                    'name' => "{$permission->name} ({$permission->description})",
                    'timestamp' => now()->toISOString(),
                ],
                'f_deleted'
            ));
        }
    }
}
