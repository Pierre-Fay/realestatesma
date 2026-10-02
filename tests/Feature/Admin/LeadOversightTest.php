<?php

use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

test('guests must sign in to view all leads', function () {
    $this->get(route('admin.leads.index'))->assertRedirect(route('login'));
});

test('agents cannot view the admin lead list', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)->get(route('admin.leads.index'))->assertForbidden();
});

test('admins see unassigned leads and leads across all agents and statuses', function () {
    $admin = User::factory()->admin()->create();
    $disabledAgent = Agent::factory()->for(User::factory()->disabled())->create();
    $leads = collect([
        Lead::factory()->create(['agent_id' => null]),
        Lead::factory()->contacted()->for($disabledAgent)->create(),
        Lead::factory()->qualified()->create(),
        Lead::factory()->closed()->create(),
        Lead::factory()->lost()->create(),
    ]);

    $this->actingAs($admin)->get(route('admin.leads.index'))
        ->assertSee($leads->pluck('email')->all())
        ->assertSee('Unassigned')
        ->assertSee($disabledAgent->name)
        ->assertViewHas('leads', fn (LengthAwarePaginator $result): bool => $result->total() === 5);
});

test('contact submissions appear in the admin lead list', function () {
    $admin = User::factory()->admin()->create();
    $this->post(route('contact.store'), [
        'first_name' => 'Jane',
        'last_name' => 'Visitor',
        'email' => 'contact-lead@example.com',
        'interested_in' => 'A villa in Centro',
        'consent' => '1',
    ])->assertRedirect(route('contact'));

    $this->actingAs($admin)->get(route('admin.leads.index'))
        ->assertSee('contact-lead@example.com')
        ->assertSee('A villa in Centro')
        ->assertViewHas('leads', fn (LengthAwarePaginator $result): bool => $result->first()->agent_id === null);
});

test('admins can filter by assignment state', function (string $assignment, bool $unassigned) {
    $admin = User::factory()->admin()->create();
    $assignedLead = Lead::factory()->create();
    $unassignedLead = Lead::factory()->create(['agent_id' => null]);

    $this->actingAs($admin)->get(route('admin.leads.index', ['assignment' => $assignment]))
        ->assertSee($unassigned ? $unassignedLead->email : $assignedLead->email)
        ->assertDontSee($unassigned ? $assignedLead->email : $unassignedLead->email);
})->with([
    'unassigned queue' => ['unassigned', true],
    'assigned leads' => ['assigned', false],
]);

test('agent and pipeline filters can be combined', function () {
    $admin = User::factory()->admin()->create();
    $agent = Agent::factory()->create();
    $match = Lead::factory()->qualified()->for($agent)->create();
    $otherStatus = Lead::factory()->closed()->for($agent)->create();
    $otherAgent = Lead::factory()->qualified()->create();
    $unassigned = Lead::factory()->qualified()->create(['agent_id' => null]);

    $this->actingAs($admin)->get(route('admin.leads.index', [
        'agent_id' => $agent->id,
        'status' => 'qualified',
        'assignment' => 'assigned',
    ]))
        ->assertSee($match->email)
        ->assertDontSee([$otherStatus->email, $otherAgent->email, $unassigned->email]);
});

test('unassigned leads appear first with newest leads first in each group', function () {
    $this->freezeTime();
    $admin = User::factory()->admin()->create();
    $assignedNew = Lead::factory()->create(['created_at' => now()]);
    $assignedOld = Lead::factory()->create(['created_at' => now()->subDays(3)]);
    $unassignedOld = Lead::factory()->create(['agent_id' => null, 'created_at' => now()->subDays(2)]);
    $unassignedNew = Lead::factory()->create(['agent_id' => null, 'created_at' => now()->subDay()]);

    $this->actingAs($admin)->get(route('admin.leads.index'))
        ->assertSeeInOrder([$unassignedNew->email, $unassignedOld->email, $assignedNew->email, $assignedOld->email]);
});

test('pagination retains filters and limits the page to fifteen leads', function () {
    $admin = User::factory()->admin()->create();
    Lead::factory()->count(16)->qualified()->create(['agent_id' => null]);

    $this->actingAs($admin)->get(route('admin.leads.index', ['assignment' => 'unassigned', 'status' => 'qualified']))
        ->assertViewHas('leads', fn (LengthAwarePaginator $result): bool => $result->count() === 15
            && $result->total() === 16
            && str_contains($result->url(2), 'assignment=unassigned')
            && str_contains($result->url(2), 'status=qualified'));
});

test('invalid lead filters are rejected', function (array $filters, string $field) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->from(route('admin.leads.index'))
        ->get(route('admin.leads.index', $filters))
        ->assertRedirect(route('admin.leads.index'))
        ->assertSessionHasErrors($field);
})->with([
    'unknown assignment state' => [['assignment' => 'anything'], 'assignment'],
    'unknown pipeline status' => [['status' => 'anything'], 'status'],
    'unknown agent' => [['agent_id' => 99999], 'agent_id'],
    'non integer agent' => [['agent_id' => '1 OR 1=1'], 'agent_id'],
    'array status' => [['status' => ['new']], 'status'],
]);

test('lead content is escaped and a zero budget is displayed', function () {
    $admin = User::factory()->admin()->create();
    $lead = Lead::factory()->create([
        'agent_id' => null,
        'first_name' => '<script>first()</script>',
        'last_name' => '<script>last()</script>',
        'interested_in' => '<script>interest()</script>',
        'notes' => '<script>notes()</script>',
        'budget' => 0,
    ]);

    $this->actingAs($admin)->get(route('admin.leads.index'))
        ->assertSee([$lead->first_name, $lead->last_name, $lead->interested_in, $lead->notes])
        ->assertDontSee([$lead->first_name, $lead->last_name, $lead->interested_in, $lead->notes], false)
        ->assertSee('$0');
});

test('an empty lead list shows an empty state', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.leads.index'))->assertSee('No leads found.');
});

test('admin lead navigation is accessible from the administration page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin'))
        ->assertSee(route('admin.leads.index'))
        ->assertSee('All leads')
        ->assertSee('Manage leads');
});
