<?php

namespace App\Models\Debugging;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['created_at_human', 'updated_at_human', 'causer_name', 'ip_address',];

    protected function createdAtHuman(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Carbon::createFromTimeString($attributes['created_at'])
                ->isoFormat('L LT')
                . ' (' . Carbon::createFromTimeString($attributes['created_at'])->diffForHumans() . ')',
        );
    }

    protected function updatedAtHuman(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Carbon::createFromTimeString($attributes['updated_at'])
                ->isoFormat('L LT')
                . ' (' . Carbon::createFromTimeString($attributes['updated_at'])->diffForHumans() . ')',
        );
    }

    protected function causerName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes)
            {
                $causerName = '';

                if (isset($attributes['causer_id']))
                {
                    $causerName = $this->causer?->name;
                }

                return $causerName;
            }
        );
    }

    protected function ipAddress(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes)
            {
                $properties = json_decode($attributes['properties'], true);

                return $properties && isset($properties['request']['ip_address'])
                    ? $properties['request']['ip_address']
                    : '';
            },
        );
    }

    public function causer(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            })
            ->when($filters['search'] ?? null, function (Builder $query, $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->whereRaw('unaccent(description) ilike unaccent(?)', ["%$term%"]);
                });
            })
            ->when($filters['sort_by'] ?? null, function (Builder $query, array $sorts)
            {
                foreach ($sorts as $field => $direction)
                {
                    switch ($field)
                    {
                        case 'causer_name':
                            $query->join('users', 'users.id', '=', 'activity_log.causer_id')
                                ->orderBy('users.name', $direction);
                            break;
                        case 'created_at_human':
                            $query->orderBy('activity_log.created_at', $direction);
                            break;
                        case 'ip_address':
                            if ($direction === 'asc' || $direction === 'desc')
                            {
                                $query->orderByRaw("(properties->'request'->>'ip_address')::inet {$direction}");
                            }
                            break;

                        default:
                            $query->orderBy($field, $direction);
                            break;
                    }
                }
            })
            ->when($filters['date'] ?? null, function (Builder $query, array $date)
            {
                $query->whereDate('created_at', "{$date['year']}-{$date['month']}-{$date['day']}");
            })
            ->when($filters['date_range'] ?? null, function (Builder $query, array $dateRange)
            {
                $start = "{$dateRange['start']['year']}-{$dateRange['start']['month']}-{$dateRange['start']['day']}";
                $end = "{$dateRange['end']['year']}-{$dateRange['end']['month']}-{$dateRange['end']['day']}";
                $query->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            })
            ->when($filters['selected_users'] ?? null, function (Builder $query, array $users)
            {
                foreach ($users as $user)
                {
                    $query->whereHasMorph(
                        'causer',
                        User::class,
                        function (Builder $query) use ($user)
                        {
                            $query->where('name', $user);
                        }
                    );
                }
            })
            ->when($filters['selected_events'] ?? null, function (Builder $query, array $events)
            {
                foreach ($events as $event)
                {
                    $query->where('event', $event);
                }
            })
            ->when($filters['selected_modules'] ?? null, function (Builder $query, array $modules)
            {
                foreach ($modules as $module)
                {
                    $query->where('log_name', $module);
                }
            })
            ->when($filters['time'] ?? null, function (Builder $query, string $time)
            {
                $query->whereTime('created_at', $time);
            })
            ->when($filters['time_from'] ?? null, function (Builder $query, string $timeFrom)
            {
                $query->whereTime('created_at', '>=', $timeFrom);
            })
            ->when($filters['time_until'] ?? null, function (Builder $query, string $timeUntil)
            {
                $query->whereTime('created_at', '<=', $timeUntil);
            })
            ->when($filters['ip_dirs'] ?? null, function (Builder $query, array $ipAddresses)
            {
                foreach ($ipAddresses as $ipAddress)
                {
                    $query->where('properties->request->ip_address', $ipAddress)
                        ->orWhere('properties->request->ip_address', $ipAddress);
                }
            });
    }
}
