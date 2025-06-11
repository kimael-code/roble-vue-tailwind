<?php

namespace App\Listeners\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogFailed
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
    public function handle(Failed $event): void
    {
        activity()
            ->event('authenticated')
            ->causedBy($event->user)
            ->withProperty('request', [
                'ip_address'      => request()->ip(),
                'user_agent'      => request()->header('user-agent'),
                'user_agent_lang' => request()->header('accept-language'),
                'referer'         => request()->header('referer'),
                'http_method'     => request()->method(),
                'request_url'     => request()->fullUrl(),
                'guard_name'      => $event->guard,
                'credentials'     => $event->credentials,
            ])
            ->withProperty('causer', User::find($event->user->id)?->toArray() ?? $event->credentials['name'])
            ->log(__(':username: failed login', ['username' => $event->user->name ?? $event->credentials['name']]));
    }
}
