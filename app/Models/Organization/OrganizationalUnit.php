<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            });
    }
}
