<?php

use App\Models\Property;

test('the about page renders', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee('About us');
});

test('the legal notice page renders', function () {
    $this->get(route('legal'))
        ->assertOk()
        ->assertSee('Legal notice');
});

test('the privacy policy page renders', function () {
    $this->get(route('privacy'))
        ->assertOk()
        ->assertSee('Privacy policy');
});

test('the public footer links to the legal notice and privacy policy', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertSee(route('legal'))
        ->assertSee(route('privacy'));
});

test('the enquiry and contact forms link to the privacy policy', function () {
    $property = Property::factory()->create(['is_active' => true, 'is_sold' => false]);

    $this->get(route('properties.show', $property))->assertOk()->assertSee(route('privacy'));
    $this->get(route('contact'))->assertOk()->assertSee(route('privacy'));
});
