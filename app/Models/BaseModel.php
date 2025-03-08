<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use LogsActivity;

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
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
