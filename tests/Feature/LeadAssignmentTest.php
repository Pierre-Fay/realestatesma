<?php

use App\Enums\LeadStatus;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

test('the assignment policy permits admins and only the owning agent', function (bool $admin, bool $hasProfile, ?int $ownerId, bool $allowed) {
    $user = $admin ? User::factory()->admin()->make() : User::factory()->make();
    $user->setRelation('agent', $hasProfile ? Agent::factory()->make(['id' => 1]) : null);
    $lead = Lead::factory()->make(['agent_id' => $ownerId]);

    expect($user->can('assign', $lead))->toBe($allowed);
})->with([
    'admin can assign unassigned lead' => [true, false, null, true],
    'admin can reassign any owner' => [true, false, 2, true],
    'owning agent can reassign' => [false, true, 1, true],
    'agent cannot reassign another owners lead' => [false, true, 2, false],
    'agent cannot claim unassigned lead' => [false, true, null, false],
    'agent without profile cannot reassign' => [false, false, 1, false],
]);

test('guests must sign in before changing a lead assignment', function (string $routeName) {
    $lead = Lead::factory()->create(['agent_id' => null]);

    $this->patch(route($routeName, $lead), ['agent_id' => null])->assertRedirect(route('login'));

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => null]);
})->with(['admin.leads.assign', 'leads.assign']);

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
    $this->patch(route('leads.assign', $lead), ['agent_id' => $agent->id])->assertForbidden();
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

test('agents can reassign their own leads and access transfers to the new owner', function () {
    $original = Agent::factory()->withUser()->create();
    $replacement = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->qualified()->for($original)->create([
        'interested_in' => 'Centro villa', 'notes' => 'Keep these notes.', 'budget' => 750000,
    ]);

    $this->actingAs($original->user)->patch(route('leads.assign', $lead), [
        'agent_id' => $replacement->id, 'status' => 'new', 'notes' => 'Unexpected overwrite',
    ])
        ->assertRedirect(route('leads.index'))
        ->assertSessionHas('status', 'lead-reassigned');

    $this->assertDatabaseHas('leads', [
        'id' => $lead->id, 'agent_id' => $replacement->id, 'status' => 'qualified',
        'interested_in' => 'Centro villa', 'notes' => 'Keep these notes.', 'budget' => 750000,
    ]);
    $this->get(route('leads.index'))->assertDontSee($lead->email);
    $this->get(route('leads.edit', $lead))->assertForbidden();
    $this->put(route('leads.update', $lead), ['interested_in' => 'Nope', 'status' => 'closed'])->assertForbidden();
    $this->patch(route('leads.status', $lead), ['status' => 'closed'])->assertForbidden();
    $this->actingAs($replacement->user)->get(route('leads.index'))->assertSee($lead->email);
    $this->get(route('leads.edit', $lead))->assertSee('Reassign lead');
    $this->patch(route('leads.status', $lead), ['status' => 'closed'])->assertRedirect(route('leads.index'));
    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $replacement->id, 'status' => 'closed']);
});

test('agents cannot reassign another agents lead or claim an unassigned lead', function (bool $unassigned) {
    $agent = Agent::factory()->withUser()->create();
    $lead = $unassigned
        ? Lead::factory()->create(['agent_id' => null])
        : Lead::factory()->create();

    $this->actingAs($agent->user)->patch(route('leads.assign', $lead), ['agent_id' => $agent->id])
        ->assertForbidden();

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $lead->agent_id]);
})->with(['another owner' => false, 'unassigned' => true]);

test('agents cannot unassign their own leads', function () {
    $agent = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($agent)->create();

    $this->actingAs($agent->user)->patch(route('leads.assign', $lead), ['agent_id' => null])
        ->assertInvalid(['agent_id' => 'The agent id field is required.']);

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $agent->id]);
});

test('agents must choose another agent for reassignment', function () {
    $agent = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($agent)->create();

    $this->actingAs($agent->user)->patch(route('leads.assign', $lead), ['agent_id' => $agent->id])
        ->assertInvalid(['agent_id' => 'Select another agent to reassign this lead.']);

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $agent->id]);
});

test('agents cannot transfer a lead to a disabled account', function () {
    $agent = Agent::factory()->withUser()->create();
    $disabled = Agent::factory()->for(User::factory()->disabled())->create();
    $lead = Lead::factory()->for($agent)->create();

    $this->actingAs($agent->user)->patch(route('leads.assign', $lead), ['agent_id' => $disabled->id])
        ->assertInvalid(['agent_id' => 'Select an agent with an enabled agent account.']);

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $agent->id]);
});

test('agents without a profile cannot change assignments', function () {
    $user = User::factory()->create();
    $lead = Lead::factory()->create(['agent_id' => null]);

    $this->actingAs($user)->patch(route('leads.assign', $lead), ['agent_id' => null])->assertForbidden();

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => null]);
});

test('admins cannot use the agent reassignment endpoint', function () {
    $admin = User::factory()->admin()->create();
    $lead = Lead::factory()->create();

    $this->actingAs($admin)->patch(route('leads.assign', $lead), ['agent_id' => null])->assertForbidden();

    $this->assertDatabaseHas('leads', ['id' => $lead->id, 'agent_id' => $lead->agent_id]);
});

test('the agent edit form offers only other eligible agents for reassignment', function () {
    $owner = Agent::factory()->withUser()->create();
    $target = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($owner)->create();

    $this->actingAs($owner->user)->get(route('leads.edit', $lead))
        ->assertSee(route('leads.assign', $lead))
        ->assertSee($target->name)
        ->assertViewHas('assignableAgents', fn (Collection $agents): bool => $agents->modelKeys() === [$target->id]);
});

test('the agent edit form explains when no other agent can receive the lead', function () {
    $owner = Agent::factory()->withUser()->create();
    $lead = Lead::factory()->for($owner)->create();

    $this->actingAs($owner->user)->get(route('leads.edit', $lead))
        ->assertSee('No other enabled agent accounts are available.')
        ->assertDontSee(route('leads.assign', $lead));
});

test('assignment requests for a missing lead return not found', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->patch(route('admin.leads.assign', 99999), ['agent_id' => null])->assertNotFound();
});
