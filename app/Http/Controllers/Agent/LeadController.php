<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateLeadStatusRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display the leads assigned to the signed-in agent.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Lead::class);

        $leads = auth()->user()->agent
            ->leads()
            ->latest()
            ->paginate(15);

        return view('agent.leads.index', ['leads' => $leads]);
    }

    /**
     * Update the status of one of the agent's leads.
     */
    public function update(UpdateLeadStatusRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $lead->update(['status' => $request->validated('status')]);

        return Redirect::route('leads.index')->with('status', 'lead-updated');
    }
}
