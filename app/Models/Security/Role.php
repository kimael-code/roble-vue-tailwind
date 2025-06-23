<?php

namespace App\Models\Security;

use App\Observers\Security\RoleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

#[ObservedBy([RoleObserver::class])]
class Role extends SpatieRole
{
    /** @use HasFactory<\Database\Factories\Security\RoleFactory> */
    use HasFactory;
    use LogsActivity;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'role';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['created_at_human', 'updated_at_human'];

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

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName(__('Security/Roles'))
            ->setDescriptionForEvent(fn(string $eventName) => __(':username: :event :model [:modelName] [:modelDescription]', [
                'username' => auth()->user()->name,
                'event' => __($eventName),
                'model' => __($this->traceObjectName),
                'modelName' => $this->name,
                'modelDescription' => $this?->description,
            ]));
    }

    public function tapActivity(Activity $activity): void
    {
        $activity->properties = $activity->properties->put('request', [
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('user-agent'),
            'user_agent_lang' => request()->header('accept-language'),
            'referer' => request()->header('referer'),
            'http_method' => request()->method(),
            'request_url' => request()->fullUrl(),
        ]);
        $activity->properties = $activity->properties->put('causer', \App\Models\User::with('person')->find(auth()->user()->id)->toArray());
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->where('name', 'ilike', "%$term%")
                        ->orWhere('description', 'ilike', "%$term%");
                });
            })
            ->when($filters['sortBy'] ?? null, function (Builder $query, array $sorts)
            {
                foreach ($sorts as $field => $direction)
                {
                    if ($field === 'record_status')
                    {
                        $query->orderBy('deleted_at', $direction);
                    }
                    else
                    {
                        $query->orderBy($field, $direction);
                    }
                }
            })
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            })
            ->when($filters['role_n'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->where('name', 'ilike', "%$term%");
                });
            });
    }
}
