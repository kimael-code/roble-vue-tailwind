<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordSet;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Activitylog\Contracts\Activity;

class LogPasswordSet
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
    public function handle(PasswordSet $event): void
    {
        activity()
            ->event('authenticated')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->withProperty('request', [
                'ip_address'      => request()->ip(),
                'user_agent'      => request()->header('user-agent'),
                'user_agent_lang' => request()->header('accept-language'),
                'referer'         => request()->header('referer'),
                'http_method'     => request()->method(),
                'request_url'     => request()->fullUrl(),
            ])
            ->withProperty('causer', User::find($event->user->id)->toArray())
            ->log(__(':username: set their own password', ['username' => $event->user->name]));
    }
}
