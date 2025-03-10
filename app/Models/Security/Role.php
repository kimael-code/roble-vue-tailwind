<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /** @use HasFactory<\Database\Factories\Security\RoleFactory> */
    use HasFactory;
    use LogsActivity;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'rol';

    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) => $this->traceObjectName.' '.__($eventName));
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
            ->when($filters['name'] ?? null, function (Builder $query, $way)
            {
                switch ($way)
                {
                    case 'u':
                        $query->orderBy('name');
                        break;
                    case 'd':
                        $query->orderBy('name', 'desc');
                        break;
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
