<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLeadAssignmentRequest;
use App\Models\Agent;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * Display all leads, including unassigned leads and completed opportunities.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $filters = $request->validate([
            'assignment' => ['nullable', Rule::in(['assigned', 'unassigned'])],
            'agent_id' => ['nullable', 'integer', Rule::exists(Agent::class, 'id')],
            'status' => ['nullable', Rule::enum(LeadStatus::class)],
        ]);

        $leads = Lead::query()
            ->with('agent')
            ->when(($filters['assignment'] ?? null) === 'assigned', fn (Builder $query) => $query->whereNotNull('agent_id'))
            ->when(($filters['assignment'] ?? null) === 'unassigned', fn (Builder $query) => $query->whereNull('agent_id'))
            ->when($filters['agent_id'] ?? null, fn (Builder $query, string $agentId) => $query->where('agent_id', $agentId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->orderByRaw('agent_id IS NOT NULL')
            ->latest()
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'agents' => Agent::query()->orderBy('name')->get(['id', 'name']),
            'assignableAgents' => Agent::query()->eligibleForLeadAssignment()->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    /**
     * Assign, reassign, or unassign a lead without changing its pipeline data.
     */
    public function assign(UpdateLeadAssignmentRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('assign', $lead);

        $lead->update($request->safe()->only(['agent_id']));

        return Redirect::route('admin.leads.index')->with('status', 'lead-assignment-updated');
    }
}
