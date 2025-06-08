<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Models\User;
use App\Observers\Organization\OrganizationalUnitObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([OrganizationalUnitObserver::class])]
class OrganizationalUnit extends BaseModel
{
    /** @use HasFactory<\Database\Factories\Organization\OrganizationalUnitFactory> */
    use HasFactory;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'unidad administrativa';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'acronym',
        'floor',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'disabled_at' => 'datetime',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status',
        'created_at_human',
        'updated_at_human',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['disabled_at']) ? 'INACTIVO' : 'ACTIVO'
        );
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function activeUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->whereNull('organizational_units.disabled_at');
    }

    public function organizationalUnits(): HasMany
    {
        return $this->hasMany(self::class);
    }

    public function activeOrganizationalUnits(): HasMany
    {
        return $this->hasMany(self::class)->whereNull('disabled_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('disabled_at');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->whereRaw('unaccent(name) ilike unaccent(?)', ["%$term%"])
                        ->orWhereRaw('unaccent(acronym) ilike unaccent(?)', ["%$term%"]);
                });
            })
            ->when($filters['sortBy'] ?? null, function (Builder $query, array $sorts)
            {
                foreach ($sorts as $field => $direction)
                {
                    switch ($field)
                    {
                        case 'status':
                            $newDirection = $direction === 'asc' ? 'desc' : 'asc';
                            $query->orderBy('organizational_units.disabled_at', $newDirection);
                            break;
                        case 'organization':
                            $query->join('organizations', 'organizations.id', '=', 'organizational_units.organization_id')
                                ->orderBy('organizations.name', $direction);
                            break;

                        default:
                            $query->orderBy($field, $direction);
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
