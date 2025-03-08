<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Authenticatable extends User
{
    use LogsActivity;

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'id',
                'name',
                'email',
                'email_verified_at',
                'current_team_id',
                'profile_photo_path',
                'created_at',
                'updated_at',
            ])
            ->dontLogIfAttributesChangedOnly(['remember_token'])
            ->setDescriptionForEvent(fn(string $eventName) => $this->traceObjectName.' '.__($eventName));
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->properties = $activity->properties->put('request', [
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->header('user-agent'),
            'user_agent_lang' => request()->header('accept-language'),
            'referer'         => request()->header('referer'),
            'http_method'     => request()->method(),
            'request_url'     => request()->fullUrl(),
        ]);
    }
}
