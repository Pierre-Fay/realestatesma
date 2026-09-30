<?php

use App\Models\Property;
use App\Models\PropertyInquiry;

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
        'name' => 'Jane Visitor',
        'email' => 'jane@example.com',
        'phone' => '+52 415 000 0000',
        'message' => 'I would like to visit this property.',
        'consent' => '1',
    ], $overrides);
}

test('guests can submit a property inquiry', function () {
    $property = inquiryProperty();

    $this->post(route('properties.inquiries.store', $property), inquiryPayload())
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'inquiry-sent');

    $this->assertDatabaseHas('property_inquiries', [
        'property_id' => $property->id,
        'name' => 'Jane Visitor',
        'email' => 'jane@example.com',
    ]);
});

test('the inquiry is validated', function () {
    $property = inquiryProperty();

    $this->from(route('properties.show', $property))
        ->post(route('properties.inquiries.store', $property), [])
        ->assertSessionHasErrors(['name', 'email', 'message', 'consent']);

    expect(PropertyInquiry::query()->count())->toBe(0);
});

test('consent is required', function () {
    $property = inquiryProperty();

    $this->post(route('properties.inquiries.store', $property), inquiryPayload(['consent' => null]))
        ->assertSessionHasErrors('consent');

    expect(PropertyInquiry::query()->count())->toBe(0);
});

test('inquiries cannot be sent for invisible properties', function () {
    $inactive = Property::factory()->create(['is_active' => false]);
    $this->post(route('properties.inquiries.store', $inactive), inquiryPayload())->assertNotFound();

    $sold = Property::factory()->create(['is_active' => true, 'is_sold' => true]);
    $this->post(route('properties.inquiries.store', $sold), inquiryPayload())->assertNotFound();

    expect(PropertyInquiry::query()->count())->toBe(0);
});

test('the honeypot silently discards bot submissions', function () {
    $property = inquiryProperty();

    $this->post(route('properties.inquiries.store', $property), inquiryPayload(['website' => 'http://spam.example']))
        ->assertRedirect(route('properties.show', $property))
        ->assertSessionHas('status', 'inquiry-sent');

    expect(PropertyInquiry::query()->count())->toBe(0);
});

test('the inquiry endpoint is rate limited', function () {
    $property = inquiryProperty();

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('properties.inquiries.store', $property), inquiryPayload())->assertRedirect();
    }

    $this->post(route('properties.inquiries.store', $property), inquiryPayload())->assertStatus(429);
});
