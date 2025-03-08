<?php

namespace App\Listeners\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogPasswordReset
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
    public function handle(PasswordReset $event): void
    {
        $causer = $event->user;

        activity()
            ->event('auth')
            ->causedBy($causer)
            ->withProperty('request', [
                'ip_address'      => request()->ip(),
                'user_agent'      => request()->header('user-agent'),
                'user_agent_lang' => request()->header('accept-language'),
                'referer'         => request()->header('referer'),
                'http_method'     => request()->method(),
                'request_url'     => request()->fullUrl(),
            ])
            ->log(__('password reset'));
    }
}
