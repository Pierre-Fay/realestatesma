<?php

use App\Enums\LeadStatus;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

test('only admins can assign, reassign, or unassign a lead', function (bool $admin, bool $hasProfile, ?int $ownerId, bool $allowed) {
    $user = $admin ? User::factory()->admin()->make() : User::factory()->make();
    $user->setRelation('agent', $hasProfile ? Agent::factory()->make(['id' => 1]) : null);
    $lead = Lead::factory()->make(['agent_id' => $ownerId]);

    expect($user->can('assign', $lead))->toBe($allowed);
})->with([
    'admin can assign unassigned lead' => [true, false, null, true],
    'admin can reassign any owner' => [true, false, 2, true],
    'owning agent cannot reassign' => [false, true, 1, false],
    'agent cannot reassign another owners lead' => [false, true, 2, false],
    'agent cannot claim unassigned lead' => [false, true, null, false],
    'agent without profile cannot reassign' => [false, false, 1, false],
]);

test('guests must sign in before changing a lead assignment', function () {
    $lead = Lead::factory()->create(['agent_id' => null]);

    $this->patch(route('admin.leads.assign', $lead), ['agent_id' => null])->assertRedirect(route('login'));

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => null]);
});

test('the agent reassignment endpoint no longer exists', function () {
    $agent = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($agent)->create();

    $this->actingAs($agent->user)
        ->patch("/leads/{$lead->id}/assignment", ['agent_id' => $agent->id])
        ->assertNotFound();
});

test('agents cannot use the admin assignment endpoint', function () {
    $agent = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($agent)->create();

    $this->actingAs($agent->user)->patch(route('admin.leads.assign', $lead), ['agent_id' => null])
        ->assertForbidden();

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $agent->id]);
});

test('admins can assign contact submissions and the receiving agent can see them', function () {
    $admin = User::factory()->admin()->create();
    $agent = Agent::factory()->withUser()->create();
    $this->post(route('contact.store'), [
        'first_name' => 'Jane',
        'last_name' => 'Visitor',
        'email' => 'assignment-contact@example.com',
        'interested_in' => 'A villa in Centro',
        'consent' => '1',
    ])->assertRedirect(route('contact'));
    $lead = Lead::query()->sole();

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), ['agent_id' => $agent->id])
        ->assertRedirect(route('admin.leads.index'))
        ->assertSessionHas('status', 'lead-assignment-updated');

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $agent->id, 'status' => 'new']);
    $this->actingAs($agent->user)->get(route('leads.index'))->assertSee($lead->email);
});

test('admins can reassign leads while preserving all pipeline and contact data', function (LeadStatus $status) {
    $admin = User::factory()->admin()->create();
    $newAgent = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Visitor',
        'email' => 'preserved@example.com',
        'phone' => '+52 415 000 0000',
        'interested_in' => 'Centro villa',
        'budget' => 750000,
        'notes' => 'Follow up next week.',
        'status' => $status,
    ]);

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), [
        'agent_id' => $newAgent->id,
        'status' => 'new',
        'notes' => 'Unexpected overwrite',
        'first_name' => 'Unexpected overwrite',
    ])->assertRedirect(route('admin.leads.index'));

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id,
        'agent_id' => $newAgent->id,
        'first_name' => 'Jane',
        'last_name' => 'Visitor',
        'email' => 'preserved@example.com',
        'phone' => '+52 415 000 0000',
        'interested_in' => 'Centro villa',
        'budget' => 750000,
        'notes' => 'Follow up next week.',
        'status' => $status->value,
    ]);
})->with(LeadStatus::cases());

test('admins can unassign a lead and its former agent loses access', function () {
    $admin = User::factory()->admin()->create();
    $agent = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->qualified()->for($agent)->create(['notes' => 'Keep these notes.']);

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), ['agent_id' => ''])
        ->assertRedirect(route('admin.leads.index'));

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id, 'agent_id' => null, 'status' => 'qualified', 'notes' => 'Keep these notes.',
    ]);
    $this->actingAs($agent->user)->get(route('leads.index'))->assertDontSee($lead->email);
    $this->get(route('leads.edit', $lead))->assertForbidden();
    $this->patch(route('leads.status', $lead), ['status' => 'closed'])->assertForbidden();
});

test('admins can recover leads assigned to disabled accounts', function () {
    $admin = User::factory()->admin()->create();
    $disabledAgent = Agent::factory()->for(User::factory()->disabled())->create();
    $replacement = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($disabledAgent)->create();

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), ['agent_id' => $replacement->id])
        ->assertRedirect(route('admin.leads.index'));

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $replacement->id]);
});

test('manual assignment rejects profiles that cannot receive leads', function (string $profile) {
    $admin = User::factory()->admin()->create();
    $lead = Lead::factory()->create(['agent_id' => null]);
    $target = match ($profile) {
        'no login' => Agent::factory()->create(),
        'disabled login' => Agent::factory()->for(User::factory()->disabled())->create(),
        'admin login' => Agent::factory()->for(User::factory()->admin())->create(),
    };

    $this->actingAs($admin)->from(route('admin.leads.index'))
        ->patch(route('admin.leads.assign', $lead), ['agent_id' => $target->id])
        ->assertRedirect(route('admin.leads.index'))
        ->assertInvalid(['agent_id' => 'Select an agent with an enabled agent account.']);

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => null]);
})->with(['no login', 'disabled login', 'admin login']);

test('an unpublished agent profile with an enabled login can receive a lead', function () {
    $admin = User::factory()->admin()->create();
    $target = Agent::factory()->withUser()->create(['is_active' => false]);
    $lead = Lead::factory()->create(['agent_id' => null]);

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), ['agent_id' => $target->id])
        ->assertRedirect(route('admin.leads.index'));

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $target->id]);
});

test('invalid agent identifiers cannot change an assignment', function (mixed $agentId, string $message) {
    $admin = User::factory()->admin()->create();
    $lead = Lead::factory()->create(['agent_id' => null]);

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), ['agent_id' => $agentId])
        ->assertInvalid(['agent_id' => $message]);

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => null]);
})->with([
    'nonexistent agent' => [99999, 'The selected agent id is invalid.'],
    'noninteger value' => ['bad', 'The agent id field must be an integer.'],
    'array value' => [[1], 'The agent id field must be an integer.'],
]);

test('omitting the agent field does not silently unassign a lead', function () {
    $admin = User::factory()->admin()->create();
    $lead = Lead::factory()->create();

    $this->actingAs($admin)->patch(route('admin.leads.assign', $lead), [])
        ->assertInvalid(['agent_id' => 'The agent id field must be present.']);

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $lead->agent_id]);
});

test('the admin assignment picker only offers eligible accounts and preserves unavailable owners', function () {
    $admin = User::factory()->admin()->create();
    $eligible = Agent::factory()->withUser()->create(['name' => 'Enabled agent']);
    $unpublished = Agent::factory()->withUser()->create(['is_active' => false]);
    $unavailable = Agent::factory()->create(['name' => 'Former agent']);
    Lead::factory()->for($unavailable)->create();

    $this->actingAs($admin)->get(route('admin.leads.index'))
        ->assertViewHas('assignableAgents', fn (Collection $agents): bool => $agents->modelKeys() === [$eligible->id, $unpublished->id]
            || $agents->modelKeys() === [$unpublished->id, $eligible->id])
        ->assertSee('Former agent — Unavailable')
        ->assertSee('Save assignment')
        ->assertSee('value="'.$eligible->id.'"', false);
});

test('admins can unassign existing leads when no enabled agent accounts are available', function () {
    $admin = User::factory()->admin()->create();
    $lead = Lead::factory()->create();

    $this->actingAs($admin)->get(route('admin.leads.index'))
        ->assertSee('No enabled agent accounts are available. Assigned leads can still be unassigned.');
    $this->patch(route('admin.leads.assign', $lead), ['agent_id' => null])
        ->assertRedirect(route('admin.leads.index'));

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => null]);
});

test('assignment requests for a missing lead return not found', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->patch(route('admin.leads.assign', 99999), ['agent_id' => null])->assertNotFound();
});
