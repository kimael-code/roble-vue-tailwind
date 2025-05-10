<?php

namespace App\Observers\Security;

use App\Models\User;
use App\Notifications\ActionHandledOnModel;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('create users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $user->id,
                    'name' => "({$user->name})",
                    'timestamp' => $user->created_at,
                ],
                'created',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('update users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $user->id,
                    'name' => "({$user->name})",
                    'timestamp' => $user->updated_at,
                ],
                'updated',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'name' => "({$user->name})",
                    'timestamp' => now()->toISOString(),
                ],
                'deleted',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('RESTORED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('restore users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $user->id,
                    'name' => "({$user->name})",
                    'timestamp' => $user->updated_at,
                ],
                'restored',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('HARD DELETED!'),
            'type'  => 'danger',
        ]);

        $users = User::permission('delete users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'name' => "({$user->name})",
                    'timestamp' => now()->toISOString(),
                ],
                'f_deleted',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }
}
