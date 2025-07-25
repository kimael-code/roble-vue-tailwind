<?php

namespace App\Models\Security;

use App\Models\User;
use App\Observers\Security\PermissionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
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
    protected $traceObjectName = 'permission';

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
            ->useLogName(__('Security/Permissions'))
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
        $activity->properties = $activity->properties
            ->put('request', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('user-agent'),
                'user_agent_lang' => request()->header('accept-language'),
                'referer' => request()->header('referer'),
                'http_method' => request()->method(),
                'request_url' => request()->fullUrl(),
            ])
            ->put('causer', User::with('person')->find(auth()->user()->id)->toArray());
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            })
            ->when($filters['search'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->where('name', 'ilike', "%$term%")
                        ->orWhere('description', 'ilike', "%$term%");
                });
            })
            ->when($filters['sort_by'] ?? null, function (Builder $query, array $sorts)
            {
                foreach ($sorts as $field => $direction)
                {
                    $query->orderBy($field, $direction);
                }
            })
            ->when($filters['roles'] ?? null, function (Builder $query, array $roles)
            {
                $roles = Role::whereIn('name', $roles)->get();

                $query->whereAttachedTo($roles);
            })
            ->when($filters['users'] ?? null, function (Builder $query, array $userEmails)
            {
                foreach ($userEmails as $userEmail)
                {
                    $query
                        ->whereHas('users', function (Builder $query) use ($userEmail)
                        {
                            $query->where('email', $userEmail);
                        })
                        ->orWhereHas('roles', function (Builder $query) use ($userEmail)
                        {
                            $user = User::where('email', $userEmail)->first();

                            foreach ($user->roles as $role)
                            {
                                $query->where('id', $role->id);
                            }
                        });
                }
            })
            ->when($filters['operations'] ?? null, function (Builder $query, array $operations)
            {
                foreach ($operations as $operation)
                {
                    switch ($operation)
                    {
                        case 'Creación':
                            $query->orWhere('name', 'ilike', '%create%');
                            break;
                        case 'Lectura':
                            $query->orWhere('name', 'ilike', '%read%');
                            break;
                        case 'Actualización':
                            $query->orWhere('name', 'ilike', '%update%');
                            break;
                        case 'Eliminación':
                            $query->orWhere('name', 'ilike', '%delete%');
                            break;
                        case 'Exportación':
                            $query->orWhere('name', 'ilike', '%export%');
                            break;
                        case 'Activación':
                            $query->orWhere('name', 'ilike', '%activate%');
                            break;
                        case 'Desactivación':
                            $query->orWhere('name', 'ilike', '%deactivate%');
                            break;
                        case 'Restauración':
                            $query->orWhere('name', 'ilike', '%restore%');
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            });
    }
}
