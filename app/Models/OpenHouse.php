<?php

namespace App\Models;

use Database\Factories\OpenHouseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'property_id',
    'starts_at',
    'ends_at',
    'notes',
    'notes_es',
    'is_published',
])]
class OpenHouse extends Model
{
    /** @use HasFactory<OpenHouseFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Property hosting this open house
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
