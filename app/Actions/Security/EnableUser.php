<?php

namespace App\Actions\Security;

use App\Models\User;
use App\Notifications\ActionHandledOnModel;

class EnableUser
{
    public static function handle(User $user): void
    {
        $user->disabled_at = null;
        $user->deleted_at = null;
        $user->save();

        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('ENABLED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('activate users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'type' => __('user'),
                    'name' => "({$user->name})",
                    'timestamp' => now()->toIso8601String(),
                ],
                'enabled',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }
}
