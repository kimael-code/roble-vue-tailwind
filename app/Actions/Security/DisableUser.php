<?php

namespace App\Actions\Security;

use App\Models\User;
use App\Notifications\ActionHandledOnModel;

class DisableUser
{
    public static function handle(User $user): void
    {
        $user->disabled_at = now()->toIso8601String();
        $user->save();

        session()->flash('message', [
            'message' => "({$user->name})",
            'title' => __('DISABLED!'),
            'type'  => 'warning',
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
