<?php

use App\Enums\CategoryGroupType;
use App\Enums\PropertyImageType;
use App\Models\Agent;
use App\Models\Category;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function agentUser(): User
{
    $user = User::factory()->create();
    Agent::factory()->create(['user_id' => $user->id]);

    return $user;
}

function validListingPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Casa Bonita',
        'address' => 'Calle 1',
        'zip' => '37700',
        'price_usd' => 500000,
        'price_mxn' => 8500000,
        'show_both_prices' => '1',
        'description' => 'A lovely home.',
        'lot_meters' => 300,
        'construction_meters' => 250,
        'bedrooms' => 3,
        'bathrooms' => 2,
        'half_bathrooms' => 1,
        'parking_spaces' => 2,
        'latitude' => 20.9,
        'longitude' => -100.7,
    ], $overrides);
}

test('guests are redirected to the login page', function () {
    $this->get(route('listings.index'))->assertRedirect(route('login'));
});

test('administrators cannot access the agent listings area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('listings.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('listings.create'))->assertForbidden();
});

test('agents can view their listings', function () {
    $this->actingAs(agentUser())
        ->get(route('listings.index'))
        ->assertOk();
});

test('agents only see their own listings', function () {
    $agent = agentUser();
    $mine = Property::factory()->create(['name' => 'My Villa']);
    $mine->agents()->attach($agent->agent);

    Property::factory()->create(['name' => 'Somebody Elses']);

    $this->actingAs($agent)
        ->get(route('listings.index'))
        ->assertOk()
        ->assertSee('My Villa')
        ->assertDontSee('Somebody Elses');
});

test('an agent can create a listing with photos and categories', function () {
    Storage::fake('public');

    $agent = agentUser();
    $type = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_TYPE]);
    $area = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_AREA]);

    $this->actingAs($agent)
        ->post(route('listings.store'), validListingPayload([
            'categories' => [$type->id, $area->id],
            'featured_image' => UploadedFile::fake()->image('cover.jpg'),
            'gallery' => [UploadedFile::fake()->image('g1.jpg'), UploadedFile::fake()->image('g2.jpg')],
        ]))
        ->assertRedirect(route('listings.index'));

    $property = Property::query()->firstOrFail();

    expect($property->is_active)->toBeFalse()
        ->and($property->is_featured)->toBeFalse()
        ->and($property->slug)->toBe('casa-bonita')
        ->and((float) $property->price_usd)->toBe(500000.0)
        ->and((float) $property->latitude)->toBe(20.9)
        ->and($property->agents->pluck('id'))->toContain($agent->agent->id)
        ->and($property->categories->pluck('id')->all())->toEqualCanonicalizing([$type->id, $area->id]);

    expect($property->images()->where('type', PropertyImageType::FEATURED_IMAGE)->count())->toBe(1);
    expect($property->images()->where('type', PropertyImageType::GALLERY)->count())->toBe(2);
    Storage::disk('public')->assertExists($property->images()->first()->path);
});

test('agents cannot assign admin-only categories', function () {
    $agent = agentUser();
    $status = Category::factory()->create(['group_type' => CategoryGroupType::PROPERTY_STATUS]);

    $this->actingAs($agent)
        ->post(route('listings.store'), validListingPayload(['categories' => [$status->id]]))
        ->assertSessionHasErrors('categories.0');

    expect(Property::query()->count())->toBe(0);
});

test('an agent can edit their own listing', function () {
    $agent = agentUser();
    $property = Property::factory()->create(['name' => 'Old name']);
    $property->agents()->attach($agent->agent);

    $this->actingAs($agent)
        ->put(route('listings.update', $property), validListingPayload(['name' => 'New name']))
        ->assertRedirect(route('listings.index'));

    expect($property->fresh()->name)->toBe('New name');
});

test('an agent cannot edit another agents listing', function () {
    $agent = agentUser();
    $other = Property::factory()->create();

    $this->actingAs($agent)->get(route('listings.edit', $other))->assertForbidden();
    $this->actingAs($agent)->put(route('listings.update', $other), validListingPayload())->assertForbidden();
});

test('listing creation validates required fields', function () {
    $agent = agentUser();

    $this->actingAs($agent)
        ->post(route('listings.store'), [])
        ->assertSessionHasErrors([
            'name', 'zip', 'price_usd', 'price_mxn', 'lot_meters',
            'construction_meters', 'bedrooms', 'bathrooms', 'half_bathrooms', 'parking_spaces',
        ]);

    expect(Property::query()->count())->toBe(0);
});

test('listings sharing a name receive distinct slugs', function () {
    $agent = agentUser();
    Property::factory()->create(['slug' => 'casa-bonita']);

    $this->actingAs($agent)->post(route('listings.store'), validListingPayload());

    expect(Property::query()->where('name', 'Casa Bonita')->first()->slug)->toBe('casa-bonita-2');
});

test('uploading a new featured image replaces the previous one', function () {
    Storage::fake('public');

    $agent = agentUser();
    $property = Property::factory()->create();
    $property->agents()->attach($agent->agent);
    $property->images()->create(['path' => 'properties/old.jpg', 'type' => PropertyImageType::FEATURED_IMAGE]);
    Storage::disk('public')->put('properties/old.jpg', 'x');

    $this->actingAs($agent)
        ->put(route('listings.update', $property), validListingPayload([
            'featured_image' => UploadedFile::fake()->image('new.jpg'),
        ]))
        ->assertRedirect(route('listings.index'));

    expect($property->images()->where('type', PropertyImageType::FEATURED_IMAGE)->count())->toBe(1);
    Storage::disk('public')->assertMissing('properties/old.jpg');
});

test('an agent can remove an image when editing', function () {
    Storage::fake('public');

    $agent = agentUser();
    $property = Property::factory()->create();
    $property->agents()->attach($agent->agent);
    $image = $property->images()->create(['path' => 'properties/g.jpg', 'type' => PropertyImageType::GALLERY]);
    Storage::disk('public')->put('properties/g.jpg', 'x');

    $this->actingAs($agent)
        ->put(route('listings.update', $property), validListingPayload(['remove_images' => [$image->id]]))
        ->assertRedirect(route('listings.index'));

    expect($property->images()->count())->toBe(0);
    Storage::disk('public')->assertMissing('properties/g.jpg');
});
