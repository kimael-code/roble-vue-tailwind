<?php

namespace App\Models\Security;

use App\Observers\Security\PermissionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[ObservedBy([PermissionObserver::class])]
class Permission extends SpatiePermission
{
    /** @use HasFactory<\Database\Factories\Security\PermissionFactory> */
    use HasFactory;
    use LogsActivity;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'permiso';

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
            ->when($filters['name'] ?? null, function (Builder $query, $dir)
            {
                switch ($dir)
                {
                    case 'asc':
                        $query->orderBy('name');
                        break;
                    case 'desc':
                        $query->orderBy('name', 'desc');
                        break;
                }
            })
            ->when($filters['description'] ?? null, function (Builder $query, $dir)
            {
                switch ($dir)
                {
                    case 'asc':
                        $query->orderBy('description');
                        break;
                    case 'desc':
                        $query->orderBy('description', 'desc');
                        break;
                }
            })
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            })
            ->when($filters['permission_n'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->where('name', 'ilike', "%$term%");
                });
            });
    }
}
