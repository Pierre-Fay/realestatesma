<?php

use App\Enums\LeadStatus;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\Property;

function inquiryProperty(array $attributes = []): Property
{
    return Property::factory()->create(array_merge([
        'is_active' => true,
        'is_sold' => false,
    ], $attributes));
}

function inquiryPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Jane',
        'last_name' => 'Visitor',
        'email' => 'jane@example.com',
        'phone' => '+52 415 000 0000',
        'message' => 'I would like to visit this property.',
        'consent' => '1',
    ], $overrides);
}

test('guests can submit a property inquiry as a lead assigned to the agent', function () {
    $property = inquiryProperty(['name' => 'Casa del Sol']);
    $agent = Agent::factory()->create(['name' => 'Sofia', 'agent_order' => 0]);
    $property->agents()->attach($agent);

    $this->post(route('properties.inquiries.store', $property), inquiryPayload())
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'inquiry-sent');

    $lead = Lead::query()->first();

    expect($lead)->not->toBeNull()
        ->and($lead->first_name)->toBe('Jane')
        ->and($lead->last_name)->toBe('Visitor')
        ->and($lead->email)->toBe('jane@example.com')
        ->and($lead->interested_in)->toBe('Casa del Sol')
        ->and($lead->notes)->toBe('I would like to visit this property.')
        ->and($lead->status)->toBe(LeadStatus::NEW)
        ->and($lead->agent_id)->toBe($agent->id);
});

test('a property inquiry with no agent leaves the lead unassigned', function () {
    $property = inquiryProperty();

    $this->post(route('properties.inquiries.store', $property), inquiryPayload())
        ->assertRedirect(route('properties.show', $property));

    expect(Lead::query()->first()->agent_id)->toBeNull();
});

test('the inquiry is validated', function () {
    $property = inquiryProperty();

    $this->from(route('properties.show', $property))
        ->post(route('properties.inquiries.store', $property), [])
        ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'message', 'consent']);

    expect(Lead::query()->count())->toBe(0);
});

test('consent is required', function () {
    $property = inquiryProperty();

    $this->post(route('properties.inquiries.store', $property), inquiryPayload(['consent' => null]))
        ->assertSessionHasErrors('consent');

    expect(Lead::query()->count())->toBe(0);
});

test('inquiries cannot be sent for invisible properties', function () {
    $inactive = Property::factory()->create(['is_active' => false]);
    $this->post(route('properties.inquiries.store', $inactive), inquiryPayload())->assertNotFound();

    $sold = Property::factory()->create(['is_active' => true, 'is_sold' => true]);
    $this->post(route('properties.inquiries.store', $sold), inquiryPayload())->assertNotFound();

    expect(Lead::query()->count())->toBe(0);
});

test('the honeypot silently discards bot submissions', function () {
    $property = inquiryProperty();

    $this->post(route('properties.inquiries.store', $property), inquiryPayload(['website' => 'http://spam.example']))
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'inquiry-sent');

    expect(Lead::query()->count())->toBe(0);
});

test('the inquiry endpoint is rate limited', function () {
    $property = inquiryProperty();

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('properties.inquiries.store', $property), inquiryPayload())->assertRedirect();
    }

    $this->post(route('properties.inquiries.store', $property), inquiryPayload())->assertStatus(429);
});
