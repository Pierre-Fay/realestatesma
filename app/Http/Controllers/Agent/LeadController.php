<?php

namespace App\Http\Controllers\Agent;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateLeadRequest;
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
     * Show the form to edit one of the agent's leads.
     */
    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);

        $labels = [
            LeadStatus::NEW->value => __('New'),
            LeadStatus::CONTACTED->value => __('Contacted'),
            LeadStatus::QUALIFIED->value => __('Qualified'),
            LeadStatus::CLOSED->value => __('Closed'),
            LeadStatus::LOST->value => __('Lost'),
        ];

        $statuses = $lead->status === LeadStatus::NEW
            ? LeadStatus::cases()
            : array_values(array_filter(LeadStatus::cases(), fn (LeadStatus $status) => $status !== LeadStatus::NEW));

        $statusOptions = collect($statuses)
            ->mapWithKeys(fn (LeadStatus $status) => [$status->value => $labels[$status->value]])
            ->all();

        return view('agent.leads.edit', [
            'lead' => $lead,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Update one of the agent's leads.
     */
    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $lead->update($request->safe()->only(['interested_in', 'budget', 'notes', 'status']));

        return Redirect::route('leads.index')->with('status', 'lead-updated');
    }

    /**
     * Quickly update the status of one of the agent's leads.
     */
    public function status(UpdateLeadStatusRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $lead->update(['status' => $request->validated('status')]);

        return Redirect::route('leads.index')->with('status', 'lead-updated');
    }
}
