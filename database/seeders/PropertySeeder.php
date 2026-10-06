<?php

namespace Database\Seeders;

use App\Enums\CategoryGroupType;
use App\Enums\PropertyImageType;
use App\Models\Agent;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Demo listings (already approved) so the public pages have data to show.
     *
     * @var list<array<string, mixed>>
     */
    private array $samples = [
        ['name' => 'Casa del Sol', 'price_usd' => 850000, 'price_mxn' => 14500000, 'bedrooms' => 4, 'bathrooms' => 3, 'half_bathrooms' => 1, 'parking_spaces' => 2, 'lot_meters' => 520, 'construction_meters' => 420],
        ['name' => 'Villa Mirador', 'price_usd' => 1850000, 'price_mxn' => 31500000, 'bedrooms' => 5, 'bathrooms' => 4, 'half_bathrooms' => 2, 'parking_spaces' => 3, 'lot_meters' => 900, 'construction_meters' => 640],
        ['name' => 'Condo Centro', 'price_usd' => 430000, 'price_mxn' => 7300000, 'bedrooms' => 2, 'bathrooms' => 2, 'half_bathrooms' => 0, 'parking_spaces' => 1, 'lot_meters' => 0, 'construction_meters' => 120],
        ['name' => 'Hacienda Guadalupe', 'price_usd' => 2400000, 'price_mxn' => 41000000, 'bedrooms' => 6, 'bathrooms' => 5, 'half_bathrooms' => 2, 'parking_spaces' => 4, 'lot_meters' => 1400, 'construction_meters' => 980],
        ['name' => 'Casa Atascadero', 'price_usd' => 675000, 'price_mxn' => 11500000, 'bedrooms' => 3, 'bathrooms' => 3, 'half_bathrooms' => 1, 'parking_spaces' => 2, 'lot_meters' => 380, 'construction_meters' => 300],
        ['name' => 'Loft San Antonio', 'price_usd' => 295000, 'price_mxn' => 5000000, 'bedrooms' => 1, 'bathrooms' => 1, 'half_bathrooms' => 0, 'parking_spaces' => 1, 'lot_meters' => 0, 'construction_meters' => 85],
    ];

    /**
     * Number of photos in the shared gallery pool.
     */
    private const GALLERY_COUNT = 6;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agent = Agent::firstOrCreate(
            ['email' => 'demo.agent@example.com'],
            ['name' => 'Sofia Ramírez', 'slug' => 'sofia-ramirez', 'is_active' => true],
        );

        $types = Category::where('group_type', CategoryGroupType::PROPERTY_TYPE)->pluck('id')->values();
        $areas = Category::where('group_type', CategoryGroupType::PROPERTY_AREA)->pluck('id')->values();
        $features = Category::where('group_type', CategoryGroupType::PROPERTY_FEATURE)->pluck('id')->values();
        $status = Category::where('group_type', CategoryGroupType::PROPERTY_STATUS)->value('id');

        foreach ($this->samples as $index => $sample) {
            $property = Property::firstOrCreate(
                ['slug' => Str::slug($sample['name'])],
                Property::factory()->raw([
                    ...$sample,
                    'slug' => Str::slug($sample['name']),
                    'address' => 'San Miguel de Allende',
                    'is_active' => true,
                    'is_sold' => false,
                    'is_featured' => $index === 0,
                    'show_both_prices' => true,
                ]),
            );

            $property->agents()->syncWithoutDetaching([$agent->id]);
            $property->categories()->syncWithoutDetaching(array_filter([
                $types->isNotEmpty() ? $types[$index % $types->count()] : null,
                $areas->isNotEmpty() ? $areas[$index % $areas->count()] : null,
                $features->isNotEmpty() ? $features[$index % $features->count()] : null,
                $status,
            ]));

            if ($property->images()->doesntExist()) {
                $this->seedImages($property, $index);
            }
        }
    }

    /**
     * Copy a listing's demo photos from the seed assets: one cover photo plus a
     * rotating selection of three photos from the shared gallery pool.
     */
    private function seedImages(Property $property, int $index): void
    {
        $base = 'properties/'.$property->slug;

        $this->copyAsset('cover-'.($index + 1).'.jpg', $base.'-cover.jpg');
        $property->images()->create([
            'path' => $base.'-cover.jpg',
            'type' => PropertyImageType::FEATURED_IMAGE,
            'sort_order' => 0,
        ]);

        for ($n = 1; $n <= 3; $n++) {
            $gallery = 'gallery-'.((($index + $n) % self::GALLERY_COUNT) + 1).'.jpg';
            $this->copyAsset($gallery, $base.'-'.$n.'.jpg');
            $property->images()->create([
                'path' => $base.'-'.$n.'.jpg',
                'type' => PropertyImageType::GALLERY,
                'sort_order' => $n,
            ]);
        }
    }

    /**
     * Copy a photo from the tracked seed assets into the public storage disk.
     */
    private function copyAsset(string $asset, string $path): void
    {
        Storage::disk('public')->put($path, file_get_contents(database_path('seeders/assets/properties/'.$asset)));
    }
}
