<?php

namespace App\Models\Debugging;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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

    public function causer(): MorphTo
    {
        return $this->morphTo()->withTrashed();
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
            ->when($filters['sortBy'] ?? null, function (Builder $query, array $sorts)
            {
                foreach ($sorts as $field => $direction)
                {
                    switch ($field)
                    {
                        case 'causer':
                            $query->join('users', 'users.id', '=', 'activity_log.causer_id')
                                ->orderBy('users.name', $direction);
                            break;
                        case 'created_at_human':
                            $query->orderBy('activity_log.created_at', $direction);
                            break;

                        default:
                            break;
                    }
                }
            })
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            });
    }
}
