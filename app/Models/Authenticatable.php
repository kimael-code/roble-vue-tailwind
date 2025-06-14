<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Authenticatable extends User
{
    use LogsActivity;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['created_at_human', 'updated_at_human', 'deleted_at_human', 'disabled_at_human',];

    protected function createdAtHuman(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes)
            {
                if ($attributes['created_at'] ?? null)
                {
                    return Carbon::createFromTimeString($attributes['created_at'])->isoFormat('L LT')
                        . ' ('
                        . Carbon::createFromTimeString($attributes['created_at'])->diffForHumans()
                        . ')';
                }
                else
                {
                    return null;
                }
            },
        );
    }

    protected function updatedAtHuman(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes)
            {
                if ($attributes['updated_at'] ?? null)
                {
                    return Carbon::createFromTimeString($attributes['updated_at'])->isoFormat('L LT')
                        . ' ('
                        . Carbon::createFromTimeString($attributes['updated_at'])->diffForHumans()
                        . ')';
                }
                else
                {
                    return null;
                }
            },
        );
    }

    protected function deletedAtHuman(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes)
            {
                if ($attributes['deleted_at'] ?? null)
                {
                    return Carbon::createFromTimeString($attributes['deleted_at'])->isoFormat('L LT')
                        . ' ('
                        . Carbon::createFromTimeString($attributes['deleted_at'])->diffForHumans()
                        . ')';
                }
                else
                {
                    return null;
                }
            },
        );
    }

    protected function disabledAtHuman(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes)
            {
                if ($attributes['disabled_at'] ?? null)
                {
                    return Carbon::createFromTimeString($attributes['disabled_at'])->isoFormat('L LT')
                        . ' ('
                        . Carbon::createFromTimeString($attributes['disabled_at'])->diffForHumans()
                        . ')';
                }
                else
                {
                    return null;
                }
            },
        );
    }

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
            ->setDescriptionForEvent(fn(string $eventName) => __(':username: :event :model', [
                'username' => auth()->user()->name,
                'event' => __($eventName),
                'model' => __($this->traceObjectName),
            ]));
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
        $activity->properties = $activity->properties->put('causer', User::find(auth()->user()->id)->toArray());
    }
}
