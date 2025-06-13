<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use App\Observers\Organization\OrganizationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[ObservedBy([OrganizationObserver::class])]
class Organization extends BaseModel
{
    /** @use HasFactory<\Database\Factories\Organization\OrganizationFactory> */
    use HasFactory;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'organization';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rif',
        'name',
        'logo_path',
        'acronym',
        'address',
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
        'logo_url',
        'status',
        'created_at_human',
        'updated_at_human',
    ];

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['logo_path'])
            ? Storage::url($attributes['logo_path'])
            : ''
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['disabled_at']) ? 'INACTIVO' : 'ACTIVO'
        );
    }

    public function organizationalUnits(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class);
    }

    public function activeOrganizationalUnits(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class)->whereNull('disabled_at');
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNull('disabled_at');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->whereRaw('unaccent(name) ilike unaccent(?)', ["%$term%"])
                        ->orWhereRaw('unaccent(rif) ilike unaccent(?)', ["%$term%"])
                        ->orWhereRaw('unaccent(acronym) ilike unaccent(?)', ["%$term%"]);
                });
            })
            ->when($filters['sortBy'] ?? null, function (Builder $query, array $sorts)
            {
                foreach ($sorts as $field => $direction)
                {
                    if ($field === 'status')
                    {
                        $newDirection = $direction === 'asc' ? 'desc' : 'asc';
                        $query->orderBy('disabled_at', $newDirection);
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
            });
    }
}
