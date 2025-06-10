<?php

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogVerified
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
    public function handle(Verified $event): void
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
            ])
            ->log(__(':username: was verified', ['username' => $event->user->name]));
    }
}
