<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordSet;
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
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->event('auth')
            ->tap(function (Activity $activity)
            {
                $activity->properties = $activity->properties->put('request', [
                    'ip_address'      => request()->ip(),
                    'user_agent'      => request()->header('user-agent'),
                    'user_agent_lang' => request()->header('accept-language'),
                    'referer'         => request()->header('referer'),
                    'http_method'     => request()->method(),
                    'request_url'     => request()->fullUrl(),
                ]);
            })
            ->log(__('password set by user'));
    }
}
