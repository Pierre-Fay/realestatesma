<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyBrowseRequest;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class PropertyBrowseController extends Controller
{
    /**
     * Public listing of approved properties, with search and filters.
     */
    public function index(PropertyBrowseRequest $request): View
    {
        $filters = $request->validated();

        $query = Property::query()
            ->where('is_active', true)
            ->where('is_sold', false)
            ->with(['images', 'categories'])
            ->when($filters['q'] ?? null, fn (Builder $q, $term) => $q->where(fn (Builder $inner) => $inner
                ->where('name', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")))
            ->when($filters['type'] ?? null, fn (Builder $q, $id) => $q->whereHas('categories', fn (Builder $c) => $c->whereKey((int) $id)))
            ->when($filters['area'] ?? null, fn (Builder $q, $id) => $q->whereHas('categories', fn (Builder $c) => $c->whereKey((int) $id)))
            ->when($filters['status'] ?? null, fn (Builder $q, $id) => $q->whereHas('categories', fn (Builder $c) => $c->whereKey((int) $id)))
            ->when($filters['bedrooms'] ?? null, fn (Builder $q, $bedrooms) => $q->where('bedrooms', '>=', (int) $bedrooms))
            ->when($filters['price_min'] ?? null, fn (Builder $q, $min) => $q->where('price_usd', '>=', $min))
            ->when($filters['price_max'] ?? null, fn (Builder $q, $max) => $q->where('price_usd', '<=', $max));

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderBy('price_usd'),
            'price_desc' => $query->orderByDesc('price_usd'),
            default => $query->latest(),
        };

        return view('properties.index', [
            'properties' => $query->paginate(12)->withQueryString(),
            'categories' => Category::filterOptions(),
        ]);
    }

    /**
     * Public detail page for one approved property.
     */
    public function show(Property $property): View
    {
        abort_unless($property->is_active && ! $property->is_sold, 404);

        $property->load(['images', 'categories', 'agents']);

        return view('properties.show', ['property' => $property]);
    }
}
