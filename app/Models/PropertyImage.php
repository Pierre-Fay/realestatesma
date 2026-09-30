<?php

namespace App\Models;

use App\Enums\PropertyImageType;
use Database\Factories\PropertyImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Whether this image is the property's featured image.
     */
    public function isFeatured(): bool
    {
        return $this->type === PropertyImageType::FEATURED_IMAGE;
    }

    /**
     * Public URL for the stored image file.
     *
     * Built from the current request host (via asset()) rather than the
     * APP_URL-based disk URL, so images resolve on whatever domain serves the app.
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => asset('storage/'.$this->path));
    }

    /**
     * Remove the stored file when the image row is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (PropertyImage $image): void {
            Storage::disk('public')->delete($image->path);
        });
    }
}
