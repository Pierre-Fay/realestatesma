<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyInquiryRequest;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class PropertyInquiryController extends Controller
{
    /**
     * Store a visitor's inquiry about a public property.
     */
    public function store(StorePropertyInquiryRequest $request, Property $property): RedirectResponse
    {
        abort_unless($property->is_active && ! $property->is_sold, 404);

        // Honeypot: a hidden field only bots fill. Pretend success without storing.
        if ($request->filled('website')) {
            return Redirect::route('properties.show', $property)->with('status', 'inquiry-sent');
        }

        $property->inquiries()->create($request->safe()->only(['name', 'email', 'phone', 'message']));

        return Redirect::route('properties.show', $property)->with('status', 'inquiry-sent');
    }
}
