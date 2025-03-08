<?php

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLockout
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
    public function handle(Lockout $event): void
    {
        $causer = $event->request->user();

        activity()
            ->event('auth')
            ->causedBy($causer)
            ->withProperty('request', [
                'ip_address'      => $event->request->ip(),
                'user_agent'      => $event->request->header('user-agent'),
                'user_agent_lang' => $event->request->header('accept-language'),
                'referer'         => $event->request->header('referer'),
                'http_method'     => $event->request->method(),
                'request_url'     => $event->request->fullUrl(),
                'credentials'     => $event->request->all(),
            ])
            ->log(__('locked user out'));
    }
}
