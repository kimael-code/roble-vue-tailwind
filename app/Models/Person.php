<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory;

    /**
     * Nombre usado para trazar el tipo de objeto.
     * @var string
     */
    protected $traceObjectName = 'person';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_card',
        'names',
        'surnames',
        'phones',
        'emails',
        'position',
        'staff_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phones' => AsCollection::class,
            'emails' => AsCollection::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $term)
            {
                $query->where(function (Builder $query) use ($term)
                {
                    $query->where('names', 'ilike', "%$term%")
                        ->orWhere('surnames', 'ilike', "%$term%");
                });
            })
            ->when(empty($filters) ?? null, function (Builder $query)
            {
                $query->latest();
            });
    }
}
