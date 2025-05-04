<?php

namespace App\Models\Organization;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends BaseModel
{
    /** @use HasFactory<\Database\Factories\Organization\OrganizationFactory> */
    use HasFactory;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'organización';

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

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->where('name', 'ilike', "%$term%")
                        ->orWhere('rif', 'ilike', "%$term%")
                        ->orWhere('acronym', 'ilike', "%$term%");
                });
            })
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            });
    }
}
