<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Http\Requests\StorePropertyInquiryRequest;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class PropertyInquiryController extends Controller
{
    /**
     * Store a visitor's inquiry about a public property as a lead
     * assigned to the property's agent.
     */
    public function store(StorePropertyInquiryRequest $request, Property $property): RedirectResponse
    {
        abort_unless($property->is_active && ! $property->is_sold, 404);

        // Honeypot: a hidden field only bots fill. Pretend success without storing.
        if ($request->filled('website')) {
            return Redirect::route('properties.show', $property)->with('status', 'inquiry-sent');
        }

        $agent = $property->agents()->orderBy('agent_order')->first();

        Lead::create([
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'interested_in' => $property->name,
            'notes' => $request->validated('message'),
            'status' => LeadStatus::NEW,
            'agent_id' => $agent?->id,
            'consented_at' => now(),
        ]);

        return Redirect::route('properties.show', $property)->with('status', 'inquiry-sent');
    }
}
