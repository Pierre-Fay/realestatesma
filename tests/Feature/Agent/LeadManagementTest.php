<?php

use App\Enums\LeadStatus;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;

function leadAgent(): User
{
    $user = User::factory()->create();
    Agent::factory()->create(['user_id' => $user->id]);

    return $user;
}

test('guests are redirected to the login page', function () {
    $this->get(route('leads.index'))->assertRedirect(route('login'));
});

test('administrators cannot access the leads area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('leads.index'))->assertForbidden();
});

test('agents only see their own leads', function () {
    $agent = leadAgent();
    Lead::factory()->create(['first_name' => 'Wilhelmina', 'last_name' => 'Zanzibar', 'agent_id' => $agent->agent->id]);
    Lead::factory()->create(['first_name' => 'Barnaby', 'last_name' => 'Quixote']);

    $this->actingAs($agent)
        ->get(route('leads.index'))
        ->assertOk()
        ->assertSee('Zanzibar')
        ->assertDontSee('Quixote');
});

test('an agent can update the status of their lead', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create(['agent_id' => $agent->agent->id, 'status' => LeadStatus::NEW]);

    $this->actingAs($agent)
        ->patch(route('leads.status', $lead), ['status' => LeadStatus::CONTACTED->value])
        ->assertRedirect(route('leads.index'));

    expect($lead->fresh()->status)->toBe(LeadStatus::CONTACTED);
});

test('an agent cannot update another agents lead', function () {
    $agent = leadAgent();
    $other = Lead::factory()->create(['status' => LeadStatus::NEW]);

    $this->actingAs($agent)
        ->patch(route('leads.status', $other), ['status' => LeadStatus::CONTACTED->value])
        ->assertForbidden();

    expect($other->fresh()->status)->toBe(LeadStatus::NEW);
});

test('the status must be a valid lead status', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create(['agent_id' => $agent->agent->id]);

    $this->actingAs($agent)
        ->followingRedirects()
        ->from(route('leads.index'))
        ->patch(route('leads.status', $lead), ['status' => 'nonsense'])
        ->assertSee('The selected status is invalid.');
});

test('a zero budget is displayed in the agent lead list', function () {
    $agent = leadAgent();
    Lead::factory()->create(['agent_id' => $agent->agent->id, 'budget' => 0]);

    $this->actingAs($agent)->get(route('leads.index'))->assertSee('$0');
});

test('full lead notes are safely rendered in the agent lead list', function () {
    $agent = leadAgent();
    $notes = "Called on Monday.\n<script>alert('unsafe')</script>\nFollow up next week.";
    Lead::factory()->create(['agent_id' => $agent->agent->id, 'notes' => $notes]);

    $this->actingAs($agent)->get(route('leads.index'))
        ->assertSee($notes)
        ->assertDontSee("<script>alert('unsafe')</script>", false);
});

test('a lead cannot be moved back to new', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create(['agent_id' => $agent->agent->id, 'status' => LeadStatus::CONTACTED]);

    $this->actingAs($agent)
        ->patch(route('leads.status', $lead), ['status' => LeadStatus::NEW->value])
        ->assertSessionHasErrors('status');

    expect($lead->fresh()->status)->toBe(LeadStatus::CONTACTED);
});

test('an agent can edit their lead', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create([
        'agent_id' => $agent->agent->id,
        'status' => LeadStatus::QUALIFIED,
        'interested_in' => 'Old interest',
        'budget' => null,
        'notes' => null,
    ]);

    $this->actingAs($agent)
        ->put(route('leads.update', $lead), [
            'interested_in' => 'A 4-bedroom villa in Centro',
            'budget' => 1200000,
            'notes' => 'Called on Monday, sending listings.',
            'status' => LeadStatus::CLOSED->value,
        ])
        ->assertRedirect(route('leads.index'));

    $lead->refresh();

    expect($lead->interested_in)->toBe('A 4-bedroom villa in Centro')
        ->and((float) $lead->budget)->toBe(1200000.0)
        ->and($lead->notes)->toBe('Called on Monday, sending listings.')
        ->and($lead->status)->toBe(LeadStatus::CLOSED);
});

test('an agent cannot edit another agents lead', function () {
    $agent = leadAgent();
    $other = Lead::factory()->create();

    $this->actingAs($agent)
        ->put(route('leads.update', $other), [
            'interested_in' => 'Nope',
            'status' => LeadStatus::CONTACTED->value,
        ])
        ->assertForbidden();
});

test('the edit form requires the interest field', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create(['agent_id' => $agent->agent->id]);

    $this->actingAs($agent)
        ->put(route('leads.update', $lead), ['interested_in' => '', 'status' => LeadStatus::CONTACTED->value])
        ->assertSessionHasErrors('interested_in');
});

test('a lead cannot be moved back to new via the edit form', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create([
        'agent_id' => $agent->agent->id,
        'status' => LeadStatus::QUALIFIED,
        'interested_in' => 'Something',
    ]);

    $this->actingAs($agent)
        ->put(route('leads.update', $lead), ['interested_in' => 'Something', 'status' => LeadStatus::NEW->value])
        ->assertSessionHasErrors('status');

    expect($lead->fresh()->status)->toBe(LeadStatus::QUALIFIED);
});
