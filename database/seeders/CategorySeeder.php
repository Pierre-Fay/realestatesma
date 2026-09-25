<?php

namespace Database\Seeders;

use App\Enums\CategoryGroupType;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Default categories per group type.
     *
     * @var array<string, list<string>>
     */
    private array $defaults = [
        'property_type' => ['House', 'Condo', 'Land', 'Villa', 'Apartment'],
        'property_area' => ['Centro', 'Guadalupe', 'San Antonio', 'Atascadero', 'Ojo de Agua'],
        'property_status' => ['For Sale', 'Under Contract', 'Pending'],
        'property_feature' => ['Pool', 'Garden', 'Rooftop Terrace', 'Furnished', 'Ocean View'],
        'property_label' => ['Featured', 'New Listing', 'Reduced Price'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->defaults as $group => $names) {
            foreach ($names as $index => $name) {
                Category::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'category_order' => $index,
                    'group_type' => CategoryGroupType::from($group),
                ]);
            }
        }
    }
}
