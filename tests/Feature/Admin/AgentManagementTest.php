<?php

use App\Enums\UserRole;
use App\Models\Agent;
use App\Models\User;

function validAgentPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Jane Doe',
        'login_email' => 'jane@example.com',
        'public_email' => 'contact@janedoe.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'phone' => '+52 415 000 0000',
        'bio' => 'Luxury property specialist.',
        'is_active' => '1',
    ], $overrides);
}

test('guests are redirected to the login page', function () {
    $this->get(route('admin.agents.index'))->assertRedirect(route('login'));
});

test('agents cannot view the agent list', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)
        ->get(route('admin.agents.index'))
        ->assertForbidden();
});

test('admins can view the agent list', function () {
    $admin = User::factory()->admin()->create();
    $agent = Agent::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.agents.index'))
        ->assertOk()
        ->assertSee($agent->email);
});

test('admins can open the create agent form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.agents.create'))
        ->assertOk();
});

test('agents cannot create an agent account', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)
        ->post(route('admin.agents.store'), validAgentPayload())
        ->assertForbidden();

    expect(User::query()->where('email', 'jane@example.com')->exists())->toBeFalse();
    expect(Agent::query()->where('email', 'contact@janedoe.com')->exists())->toBeFalse();
});

test('an admin can create an agent account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.agents.store'), validAgentPayload())
        ->assertRedirect(route('admin.agents.index'));

    $user = User::query()->where('email', 'jane@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Jane Doe')
        ->and($user->role)->toBe(UserRole::AGENT)
        ->and($user->is_enabled)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();

    $agent = Agent::query()->where('email', 'contact@janedoe.com')->first();
    expect($agent)->not->toBeNull()
        ->and($agent->name)->toBe('Jane Doe')
        ->and($agent->slug)->toBe('jane-doe')
        ->and($agent->phone)->toBe('+52 415 000 0000')
        ->and($agent->bio)->toBe('Luxury property specialist.')
        ->and($agent->is_active)->toBeTrue()
        ->and($agent->user_id)->toBe($user->id);
});

test('a newly created agent can sign in and reach the dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.agents.store'), validAgentPayload())
        ->assertRedirect(route('admin.agents.index'));

    $this->post(route('logout'));

    $this->post(route('login'), [
        'email' => 'jane@example.com',
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->get(route('dashboard'))->assertOk();
});

test('the login email must be unique', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin)
        ->post(route('admin.agents.store'), validAgentPayload(['login_email' => 'taken@example.com']))
        ->assertSessionHasErrors('login_email');

    expect(Agent::query()->where('email', 'contact@janedoe.com')->exists())->toBeFalse();
});

test('the public email must be unique', function () {
    $admin = User::factory()->admin()->create();
    Agent::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin)
        ->post(route('admin.agents.store'), validAgentPayload(['public_email' => 'taken@example.com']))
        ->assertSessionHasErrors('public_email');
});

test('the password must be confirmed', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.agents.store'), validAgentPayload(['password_confirmation' => 'different']))
        ->assertSessionHasErrors('password');
});

test('agents sharing a name receive distinct slugs', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.agents.store'), validAgentPayload([
        'login_email' => 'one@example.com',
        'public_email' => 'public-one@example.com',
    ]));

    $this->actingAs($admin)->post(route('admin.agents.store'), validAgentPayload([
        'login_email' => 'two@example.com',
        'public_email' => 'public-two@example.com',
    ]));

    $slugs = Agent::query()->where('name', 'Jane Doe')->pluck('slug');

    expect($slugs)->toHaveCount(2)
        ->and($slugs->unique())->toHaveCount(2)
        ->and($slugs)->toContain('jane-doe');
});

test('an agent created with the profile hidden is inactive', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.agents.store'), validAgentPayload(['is_active' => '0']))
        ->assertRedirect(route('admin.agents.index'));

    expect(Agent::query()->where('email', 'contact@janedoe.com')->first()->is_active)->toBeFalse();
});
