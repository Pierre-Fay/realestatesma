<?php

use App\Enums\PropertyImageType;
use App\Models\Agent;
use App\Models\Property;

function visibleProperty(array $attributes = []): Property
{
    return Property::factory()->create(array_merge([
        'is_active' => true,
        'is_sold' => false,
    ], $attributes));
}

test('guests can view an approved property detail', function () {
    $property = visibleProperty([
        'name' => 'Villa Test',
        'description' => 'A wonderful villa.',
        'price_usd' => 1234567,
    ]);

    $this->get(route('properties.show', $property))
        ->assertOk()
        ->assertSee('Villa Test')
        ->assertSee('1,234,567')
        ->assertSee('A wonderful villa.');
});

test('inactive properties are not found', function () {
    $property = Property::factory()->create(['is_active' => false]);

    $this->get(route('properties.show', $property))->assertNotFound();
});

test('sold properties are not found', function () {
    $property = Property::factory()->create(['is_active' => true, 'is_sold' => true]);

    $this->get(route('properties.show', $property))->assertNotFound();
});

test('unknown slugs are not found', function () {
    $this->get('/properties/does-not-exist')->assertNotFound();
});

test('the MXN price is shown only when the flag is on', function () {
    $both = visibleProperty(['show_both_prices' => true]);
    $this->get(route('properties.show', $both))->assertOk()->assertSee('MX$');

    $usdOnly = visibleProperty(['show_both_prices' => false]);
    $this->get(route('properties.show', $usdOnly))->assertOk()->assertDontSee('MX$');
});

test('the listing agent contact is shown', function () {
    $agent = Agent::factory()->create([
        'name' => 'Sofia Ramírez',
        'phone' => '+52 415 111 2222',
        'email' => 'sofia@example.com',
    ]);
    $property = visibleProperty();
    $property->agents()->attach($agent);

    $this->get(route('properties.show', $property))
        ->assertOk()
        ->assertSee('Sofia Ramírez')
        ->assertSee('+52 415 111 2222')
        ->assertSee('sofia@example.com');
});

test('property images are rendered', function () {
    $property = visibleProperty();
    $property->images()->create(['path' => 'properties/cover.jpg', 'type' => PropertyImageType::FEATURED_IMAGE, 'sort_order' => 0]);
    $property->images()->create(['path' => 'properties/gallery-1.jpg', 'type' => PropertyImageType::GALLERY, 'sort_order' => 1]);

    $this->get(route('properties.show', $property))
        ->assertOk()
        ->assertSee('properties/cover.jpg')
        ->assertSee('properties/gallery-1.jpg');
});

test('the browsing cards link to the detail page', function () {
    $property = visibleProperty(['name' => 'Linkable Home']);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee(route('properties.show', $property));
});
