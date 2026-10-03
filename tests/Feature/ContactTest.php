<?php

use App\Enums\LeadStatus;
use App\Models\Lead;

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Jane',
        'last_name' => 'Visitor',
        'email' => 'jane@example.com',
        'phone' => '+52 415 000 0000',
        'interested_in' => 'A 3-bedroom villa in Centro',
        'budget' => 750000,
        'consent' => '1',
    ], $overrides);
}

test('guests can view the contact page', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('Contact us');
});

test('guests can submit a contact request', function () {
    $this->post(route('contact.store'), contactPayload())
        ->assertRedirect(route('contact'))
        ->assertSessionHas('status', 'lead-sent');

    $lead = Lead::query()->first();

    expect($lead)->not->toBeNull()
        ->and($lead->first_name)->toBe('Jane')
        ->and($lead->last_name)->toBe('Visitor')
        ->and($lead->email)->toBe('jane@example.com')
        ->and($lead->interested_in)->toBe('A 3-bedroom villa in Centro')
        ->and($lead->status)->toBe(LeadStatus::NEW)
        ->and($lead->agent_id)->toBeNull()
        ->and($lead->consented_at)->not->toBeNull();
});

test('the contact request is validated', function () {
    $this->from(route('contact'))
        ->post(route('contact.store'), [])
        ->assertSessionHasErrors(['first_name', 'last_name', 'email', 'interested_in', 'consent']);

    expect(Lead::query()->count())->toBe(0);
});

test('consent is required', function () {
    $this->post(route('contact.store'), contactPayload(['consent' => null]))
        ->assertSessionHasErrors('consent');

    expect(Lead::query()->count())->toBe(0);
});

test('the email is limited to the column length', function () {
    $this->post(route('contact.store'), contactPayload(['email' => str_repeat('a', 95).'@example.com']))
        ->assertSessionHasErrors('email');

    expect(Lead::query()->count())->toBe(0);
});

test('the honeypot silently discards bot submissions', function () {
    $this->post(route('contact.store'), contactPayload(['website' => 'http://spam.example']))
        ->assertRedirect(route('contact'))
        ->assertSessionHas('status', 'lead-sent');

    expect(Lead::query()->count())->toBe(0);
});

test('the contact endpoint is rate limited', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('contact.store'), contactPayload())->assertRedirect();
    }

    $this->post(route('contact.store'), contactPayload())->assertStatus(429);
});
