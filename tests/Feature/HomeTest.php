<?php

use App\Models\Property;

function homeProperty(array $attributes = []): Property
{
    return Property::factory()->create(array_merge([
        'is_active' => true,
        'is_sold' => false,
    ], $attributes));
}

test('the homepage renders and replaced the stock welcome', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Luxury real estate in San Miguel de Allende')
        ->assertDontSee('Let\'s get started');
});

test('the homepage slider shows approved listings', function () {
    homeProperty(['name' => 'Slider Villa', 'is_featured' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Slider Villa');
});

test('the homepage does not show pending listings', function () {
    Property::factory()->create(['name' => 'Hidden Pending', 'is_active' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Hidden Pending');
});

test('the homepage search posts to the properties listing', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('action="'.route('properties.index').'"', false)
        ->assertSee('name="type"', false)
        ->assertSee('name="area"', false);
});

test('the homepage shows the multilingual section', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('We are multilingual & multicultural')
        ->assertSee('images/flags/us.svg', false)
        ->assertSee('images/flags/it.svg', false);
});
