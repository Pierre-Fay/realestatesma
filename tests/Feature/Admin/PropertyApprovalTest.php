<?php

use App\Enums\PropertyImageType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function approvalProperty(array $attributes = []): Property
{
    return Property::factory()->create(array_merge([
        'is_active' => false,
        'is_sold' => false,
    ], $attributes));
}

test('guests are redirected to the login page', function () {
    $this->get(route('admin.properties.index'))->assertRedirect(route('login'));
});

test('agents cannot access the property approval area', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)->get(route('admin.properties.index'))->assertForbidden();
});

test('admins can view the property approval list', function () {
    $admin = User::factory()->admin()->create();
    approvalProperty(['name' => 'Pending Palace']);

    $this->actingAs($admin)
        ->get(route('admin.properties.index'))
        ->assertOk()
        ->assertSee('Pending Palace');
});

test('an admin can approve a property', function () {
    $admin = User::factory()->admin()->create();
    $property = approvalProperty();

    $this->actingAs($admin)
        ->patch(route('admin.properties.approve', $property))
        ->assertRedirect(route('admin.properties.index'));

    expect($property->fresh()->is_active)->toBeTrue();
});

test('an approved property becomes publicly visible', function () {
    $admin = User::factory()->admin()->create();
    $property = approvalProperty(['name' => 'Newly Live']);

    $this->actingAs($admin)->patch(route('admin.properties.approve', $property));

    $this->get(route('properties.index'))->assertOk()->assertSee('Newly Live');
    $this->get(route('properties.show', $property))->assertOk();
});

test('an admin can unpublish a property', function () {
    $admin = User::factory()->admin()->create();
    $property = approvalProperty(['is_active' => true]);

    $this->actingAs($admin)
        ->patch(route('admin.properties.unpublish', $property))
        ->assertRedirect(route('admin.properties.index'));

    expect($property->fresh()->is_active)->toBeFalse();
});

test('an admin can delete a property and its image files', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $property = approvalProperty();
    Storage::disk('public')->put('properties/cover.jpg', 'x');
    $property->images()->create(['path' => 'properties/cover.jpg', 'type' => PropertyImageType::FEATURED_IMAGE, 'sort_order' => 0]);

    $this->actingAs($admin)
        ->delete(route('admin.properties.destroy', $property))
        ->assertRedirect(route('admin.properties.index'));

    expect(Property::query()->whereKey($property->id)->exists())->toBeFalse();
    Storage::disk('public')->assertMissing('properties/cover.jpg');
});

test('agents cannot approve a property', function () {
    $agent = User::factory()->create();
    $property = approvalProperty();

    $this->actingAs($agent)
        ->patch(route('admin.properties.approve', $property))
        ->assertForbidden();

    expect($property->fresh()->is_active)->toBeFalse();
});
