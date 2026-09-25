<?php

namespace App\Models;

use App\Enums\PropertyImageType;
use Database\Factories\PropertyImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'property_id',
    'path',
    'type',
    'sort_order',
])]
class PropertyImage extends Model
{
    /** @use HasFactory<PropertyImageFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PropertyImageType::class,
            'sort_order' => 'integer',
        ];
    }

    /**
     * Property owning this image
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
