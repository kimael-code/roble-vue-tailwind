<?php

namespace App\Listeners\Auth;

use App\Models\User;
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
            ->withProperty('causer', User::with('person')->find($event->user->id)->toArray())
            ->log(__(':username: reset their password', ['username' => $event->user->name]));
    }
}
