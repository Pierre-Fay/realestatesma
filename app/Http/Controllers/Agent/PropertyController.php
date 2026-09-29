<?php

namespace App\Http\Controllers\Agent;

use App\Enums\CategoryGroupType;
use App\Enums\PropertyImageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StorePropertyRequest;
use App\Http\Requests\Agent\UpdatePropertyRequest;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display the listings owned by the signed-in agent.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Property::class);

        $properties = auth()->user()->agent
            ->properties()
            ->with(['categories', 'images'])
            ->latest()
            ->paginate(15);

        return view('agent.properties.index', ['properties' => $properties]);
    }

    /**
     * Show the form to create a new listing.
     */
    public function create(): View
    {
        $this->authorize('create', Property::class);

        return view('agent.properties.create', ['categories' => $this->categoryOptions()]);
    }

    /**
     * Store a new listing for the signed-in agent.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $this->authorize('create', Property::class);

        $data = $request->validated();

        DB::transaction(function () use ($request, $data): void {
            $property = Property::create($this->attributes($data, $request) + [
                'slug' => Property::uniqueSlugFor($data['name']),
                'is_active' => false,
                'is_featured' => false,
            ]);

            $property->agents()->attach($request->user()->agent);
            $property->categories()->sync($data['categories'] ?? []);

            $this->storeFeaturedImage($property, $request);
            $this->storeGallery($property, $request);
        });

        return Redirect::route('listings.index')->with('status', 'listing-created');
    }

    /**
     * Show the form to edit one of the agent's listings.
     */
    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        $property->load(['categories', 'images']);

        return view('agent.properties.edit', [
            'property' => $property,
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Update one of the agent's listings.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $property): void {
            $property->update($this->attributes($data, $request) + [
                'slug' => Property::uniqueSlugFor($data['name'], $property->getKey()),
            ]);

            $property->categories()->sync($data['categories'] ?? []);

            $property->images()->whereIn('id', $data['remove_images'] ?? [])->get()->each->delete();

            if ($request->file('featured_image')) {
                $property->images()->where('type', PropertyImageType::FEATURED_IMAGE)->get()->each->delete();
                $this->storeFeaturedImage($property, $request);
            }

            $this->storeGallery($property, $request);
        });

        return Redirect::route('listings.index')->with('status', 'listing-updated');
    }

    /**
     * Map the validated input to property columns (shared by create and update).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data, Request $request): array
    {
        return [
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'zip' => $data['zip'],
            'price_usd' => $data['price_usd'],
            'price_mxn' => $data['price_mxn'],
            'show_both_prices' => $request->boolean('show_both_prices'),
            'description' => $data['description'] ?? null,
            'lot_meters' => $data['lot_meters'],
            'construction_meters' => $data['construction_meters'],
            'bedrooms' => $data['bedrooms'],
            'bathrooms' => $data['bathrooms'],
            'half_bathrooms' => $data['half_bathrooms'],
            'parking_spaces' => $data['parking_spaces'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ];
    }

    private function storeFeaturedImage(Property $property, Request $request): void
    {
        if ($file = $request->file('featured_image')) {
            $property->images()->create([
                'path' => $file->store('properties', 'public'),
                'type' => PropertyImageType::FEATURED_IMAGE,
            ]);
        }
    }

    private function storeGallery(Property $property, Request $request): void
    {
        $sortOrder = (int) $property->images()
            ->where('type', PropertyImageType::GALLERY)
            ->max('sort_order');

        foreach ($request->file('gallery', []) as $file) {
            $property->images()->create([
                'path' => $file->store('properties', 'public'),
                'type' => PropertyImageType::GALLERY,
                'sort_order' => ++$sortOrder,
            ]);
        }
    }

    /**
     * Categories an agent is allowed to assign, grouped by group type.
     *
     * @return Collection<string, Collection<int, Category>>
     */
    private function categoryOptions(): Collection
    {
        return Category::query()
            ->whereIn('group_type', [
                CategoryGroupType::PROPERTY_TYPE,
                CategoryGroupType::PROPERTY_AREA,
                CategoryGroupType::PROPERTY_FEATURE,
            ])
            ->orderBy('category_order')
            ->get()
            ->groupBy(fn (Category $category): string => $category->group_type->value);
    }
}
