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
        ->patch(route('leads.update', $lead), ['status' => LeadStatus::CONTACTED->value])
        ->assertRedirect(route('leads.index'));

    expect($lead->fresh()->status)->toBe(LeadStatus::CONTACTED);
});

test('an agent cannot update another agents lead', function () {
    $agent = leadAgent();
    $other = Lead::factory()->create(['status' => LeadStatus::NEW]);

    $this->actingAs($agent)
        ->patch(route('leads.update', $other), ['status' => LeadStatus::CONTACTED->value])
        ->assertForbidden();

    expect($other->fresh()->status)->toBe(LeadStatus::NEW);
});

test('the status must be a valid lead status', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create(['agent_id' => $agent->agent->id]);

    $this->actingAs($agent)
        ->patch(route('leads.update', $lead), ['status' => 'nonsense'])
        ->assertSessionHasErrors('status');
});

test('a lead cannot be moved back to new', function () {
    $agent = leadAgent();
    $lead = Lead::factory()->create(['agent_id' => $agent->agent->id, 'status' => LeadStatus::CONTACTED]);

    $this->actingAs($agent)
        ->patch(route('leads.update', $lead), ['status' => LeadStatus::NEW->value])
        ->assertSessionHasErrors('status');

    expect($lead->fresh()->status)->toBe(LeadStatus::CONTACTED);
});
