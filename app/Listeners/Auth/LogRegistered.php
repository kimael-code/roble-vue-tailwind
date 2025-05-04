<?php

namespace App\Listeners\Auth;

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
        $causer = auth()->user();

        activity()
            ->event('auth')
            ->causedBy($causer)
            ->performedOn($event->user)
            ->withProperty('request', [
                'ip_address'      => request()->ip(),
                'user_agent'      => request()->header('user-agent'),
                'user_agent_lang' => request()->header('accept-language'),
                'referer'         => request()->header('referer'),
                'http_method'     => request()->method(),
                'request_url'     => request()->fullUrl(),
            ])
            ->log(__('user registered'));
    }
}
