<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display all properties, pending approval first.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $status = $request->query('status');
        $status = in_array($status, ['pending', 'live', 'sold'], true) ? $status : null;

        $properties = Property::query()
            ->with(['agents', 'categories', 'images'])
            ->when($status === 'pending', fn (Builder $q) => $q->where('is_active', false)->where('is_sold', false))
            ->when($status === 'live', fn (Builder $q) => $q->where('is_active', true)->where('is_sold', false))
            ->when($status === 'sold', fn (Builder $q) => $q->where('is_sold', true))
            ->orderBy('is_active')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.properties.index', [
            'properties' => $properties,
            'status' => $status,
        ]);
    }

    /**
     * Publish a property (approve).
     */
    public function approve(Property $property): RedirectResponse
    {
        $this->authorize('approve', $property);

        $property->update(['is_active' => true]);

        return Redirect::route('admin.properties.index')->with('status', 'property-approved');
    }

    /**
     * Withdraw a property from the market.
     */
    public function unpublish(Property $property): RedirectResponse
    {
        $this->authorize('unpublish', $property);

        $property->update(['is_active' => false]);

        return Redirect::route('admin.properties.index')->with('status', 'property-unpublished');
    }

    /**
     * Delete a property (and its image files).
     */
    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return Redirect::route('admin.properties.index')->with('status', 'property-deleted');
    }
}
