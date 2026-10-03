<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Show the general contact form.
     */
    public function create(): View
    {
        return view('contact');
    }

    /**
     * Store a visitor's general contact request as an unassigned lead.
     */
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        // Honeypot: a hidden field only bots fill. Pretend success without storing.
        if ($request->filled('website')) {
            return Redirect::route('contact')->with('status', 'lead-sent');
        }

        Lead::create($request->safe()->only([
            'first_name', 'last_name', 'email', 'phone', 'interested_in', 'budget',
        ]) + [
            'status' => LeadStatus::NEW,
            'consented_at' => now(),
        ]);

        return Redirect::route('contact')->with('status', 'lead-sent');
    }
}
