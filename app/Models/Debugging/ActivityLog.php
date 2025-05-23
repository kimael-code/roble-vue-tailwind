<?php

namespace App\Models\Debugging;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Activity
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['created_at_human', 'updated_at_human'];

    protected function createdAtHuman(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Carbon::createFromTimeString($attributes['created_at'])->isoFormat('L LT') . ' (' . Carbon::createFromTimeString($attributes['created_at'])->diffForHumans() . ')',
        );
    }

    protected function updatedAtHuman(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Carbon::createFromTimeString($attributes['updated_at'])->isoFormat('L LT') . ' (' . Carbon::createFromTimeString($attributes['updated_at'])->diffForHumans() . ')',
        );
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $query, $term)
        {
            $query->where(function (Builder $query) use ($term)
            {
                $query->whereRaw('unaccent(description) ilike unaccent(?)', ["%$term%"]);
            });
        })
        ->when($filters['user'] ?? null, function (Builder $query, $userID)
        {
            $query->whereHasMorph(
                'causer',
                User::class,
                function (Builder $query) use ($userID)
                {
                    $query->where('users.id', '=', $userID);
                }
            );
        })
        ->when($filters['user_ids'] ?? null, function (Builder $query, $ids)
        {
            $query->whereIn('causer_id', $ids);
        })
        ->when($filters['event_types'] ?? null, function (Builder $query, $ids)
        {
            $query->whereIn('event', $ids);
        })
        ->when($filters['ts_b'] ?? null, function (Builder $query, $ts)
        {
            $query->where('created_at', '>=', $ts);
        })
        ->when($filters['ts_e'] ?? null, function (Builder $query, $ts)
        {
            $query->where('created_at', '<=', $ts);
        })
        ->when($filters['ts_h'] ?? null, function (Builder $query, $ts)
        {
            if (in_array('ytd', $ts, true) && in_array('now', $ts, true))
            {
                $query->whereDate('created_at', now()->toDateString())
                    ->orWhereDate('created_at', Carbon::yesterday()->toDateString());
            }
            else
            {
                foreach ($ts as $value)
                {
                    if ($value === 'now')
                    {
                        $query->whereDate('created_at', now()->toDateString());
                    }
                    if ($value === 'ytd')
                    {
                        $query->whereDate('created_at', Carbon::yesterday()->toDateString());
                    }
                }
            }
        })
        ->when($filters['d'] ?? null, function (Builder $query, $way)
        {
            switch ($way)
            {
                case 'u':
                    $query->orderBy('description');
                    break;
                case 'd':
                    $query->orderBy('description', 'desc');
                    break;
            }
        })
        ->when($filters['cn'] ?? null, function (Builder $query, $way)
        {
            switch ($way)
            {
                case 'u':
                    $query->orderBy('causer_id');
                    break;
                case 'd':
                    $query->orderBy('causer_id', 'desc');
                    break;
            }
        })
        ->when($filters['cah'] ?? null, function (Builder $query, $way)
        {
            switch ($way)
            {
                case 'u':
                    $query->orderBy('created_at');
                    break;
                case 'd':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        })
        ->when(empty($filters) ?? null, function (Builder $query)
        {
            $query->latest();
        });
    }
}
