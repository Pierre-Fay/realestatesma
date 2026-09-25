<?php

namespace App\Models;

use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'address',
    'city',
    'state',
    'country',
    'zip',
    'latitude',
    'longitude',
    'is_featured',
    'is_active',
    'is_sold',
    'sold_date',
    'price_usd',
    'price_mxn',
    'show_both_prices',
    'description',
    'description_es',
    'lot_meters',
    'construction_meters',
    'bedrooms',
    'bathrooms',
    'half_bathrooms',
    'parking_spaces',
    'notes',
    'property_order',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_sold' => 'boolean',
            'show_both_prices' => 'boolean',
            'sold_date' => 'date',
            'price_usd' => 'decimal:2',
            'price_mxn' => 'decimal:2',
            'lot_meters' => 'float',
            'construction_meters' => 'float',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'half_bathrooms' => 'integer',
            'parking_spaces' => 'integer',
            'property_order' => 'integer',
        ];
    }

    /**
     * Agents assigned to this property
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class);
    }

    /**
     * Categories assigned to this property
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Images for the property
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    /**
     * Open house events of the property
     */
    public function openHouses(): HasMany
    {
        return $this->hasMany(OpenHouse::class);
    }

    /**
     * Inquiries submitted for the property
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }
}
