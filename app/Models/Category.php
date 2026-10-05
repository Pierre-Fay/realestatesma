<?php

namespace App\Models;

use App\Enums\CategoryGroupType;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

#[Fillable([
    'name',
    'slug',
    'category_order',
    'group_type',
])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category_order' => 'integer',
            'group_type' => CategoryGroupType::class,
        ];
    }

    /**
     * Properties classified under this category
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    /**
     * Options for the public property filter selects, grouped by group type.
     *
     * @return array{type: Collection<int, self>, area: Collection<int, self>}
     */
    public static function filterOptions(): array
    {
        $options = fn (CategoryGroupType $group): Collection => static::query()
            ->where('group_type', $group)
            ->orderBy('category_order')
            ->get();

        return [
            'type' => $options(CategoryGroupType::PROPERTY_TYPE),
            'area' => $options(CategoryGroupType::PROPERTY_AREA),
        ];
    }
}
