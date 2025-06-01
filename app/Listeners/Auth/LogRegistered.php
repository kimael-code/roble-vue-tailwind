<?php

namespace App\Listeners\Auth;

use App\Models\User;
use App\Notifications\ActionHandledOnModel;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogRegistered
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        session()->flash('message', [
            'message' => "({$event->user->name})",
            'title' => __('SAVED!'),
            'type'  => 'success',
        ]);

        $users = User::permission('create new users')->get()->filter(
            fn (User $user) => $user->id != auth()->user()->id
        )->all();

        foreach ($users as $user)
        {
            $user->notify(new ActionHandledOnModel(
                auth()->user(),
                [
                    'id' => $user->id,
                    'type' => __('user'),
                    'name' => "({$user->name})",
                    'timestamp' => $user->created_at,
                ],
                'created',
                ['routeName' => 'users', 'routeParam' => 'user']
            ));
        }
    }
}
