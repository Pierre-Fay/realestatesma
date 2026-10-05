<?php

use App\Enums\CategoryGroupType;
use App\Models\Category;
use App\Models\Property;

function activeProperty(array $attributes = []): Property
{
    return Property::factory()->create(array_merge([
        'is_active' => true,
        'is_sold' => false,
    ], $attributes));
}

test('guests can browse the public property listing', function () {
    activeProperty(['name' => 'Casa Visible']);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('Casa Visible');
});

test('inactive and sold properties are hidden', function () {
    activeProperty(['name' => 'Active Home']);
    Property::factory()->create(['name' => 'Pending Home', 'is_active' => false]);
    Property::factory()->create(['name' => 'Sold Home', 'is_active' => true, 'is_sold' => true]);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('Active Home')
        ->assertDontSee('Pending Home')
        ->assertDontSee('Sold Home');
});

test('properties can be filtered by type', function () {
    $type = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_TYPE, 'name' => 'Villa']);
    activeProperty(['name' => 'Villa Match'])->categories()->attach($type);
    activeProperty(['name' => 'Other Home']);

    $this->get(route('properties.index', ['type' => $type->id]))
        ->assertOk()
        ->assertSee('Villa Match')
        ->assertDontSee('Other Home');
});

test('properties can be filtered by area', function () {
    $area = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_AREA, 'name' => 'Centro']);
    activeProperty(['name' => 'Centro Match'])->categories()->attach($area);
    activeProperty(['name' => 'Elsewhere Home']);

    $this->get(route('properties.index', ['area' => $area->id]))
        ->assertOk()
        ->assertSee('Centro Match')
        ->assertDontSee('Elsewhere Home');
});

test('properties can be filtered by minimum bedrooms', function () {
    activeProperty(['name' => 'Small Home', 'bedrooms' => 2]);
    activeProperty(['name' => 'Big Home', 'bedrooms' => 5]);

    $this->get(route('properties.index', ['bedrooms' => 4]))
        ->assertOk()
        ->assertSee('Big Home')
        ->assertDontSee('Small Home');
});

test('properties can be filtered by price range', function () {
    activeProperty(['name' => 'Cheap Home', 'price_usd' => 100000]);
    activeProperty(['name' => 'Pricey Home', 'price_usd' => 2000000]);

    $this->get(route('properties.index', ['price_min' => 500000, 'price_max' => 3000000]))
        ->assertOk()
        ->assertSee('Pricey Home')
        ->assertDontSee('Cheap Home');
});

test('properties can be searched by keyword', function () {
    activeProperty(['name' => 'Casa Bonita', 'address' => 'Centro']);
    activeProperty(['name' => 'Villa Rosa', 'address' => 'Guadalupe']);

    $this->get(route('properties.index', ['q' => 'Bonita']))
        ->assertOk()
        ->assertSee('Casa Bonita')
        ->assertDontSee('Villa Rosa');
});

test('properties can be sorted by price', function () {
    activeProperty(['name' => 'Cheap Home', 'price_usd' => 100000]);
    activeProperty(['name' => 'Pricey Home', 'price_usd' => 2000000]);

    $this->get(route('properties.index', ['sort' => 'price_asc']))
        ->assertOk()
        ->assertSeeInOrder(['Cheap Home', 'Pricey Home']);

    $this->get(route('properties.index', ['sort' => 'price_desc']))
        ->assertOk()
        ->assertSeeInOrder(['Pricey Home', 'Cheap Home']);
});

test('the filter selects submit category ids, not labels', function () {
    $type = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_TYPE, 'name' => 'Villa']);
    $area = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_AREA, 'name' => 'Centro']);

    $this->get(route('properties.index'))
        ->assertOk()
        ->assertSee('value="'.$type->id.'"', false)
        ->assertSee('value="'.$area->id.'"', false)
        ->assertDontSee('value="Villa"', false)
        ->assertDontSee('value="Centro"', false);
});

test('filters accept browser query values without error', function () {
    activeProperty(['name' => 'Some Home', 'bedrooms' => 5]);

    $this->get('/properties?q=&type=&area=&bedrooms=5&price_min=&price_max=&sort=newest')
        ->assertOk()
        ->assertSee('Some Home');
});
