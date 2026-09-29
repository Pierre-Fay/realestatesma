<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

test('guests are redirected to the login page', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
});

test('agents cannot view the user management page', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admins can view the user management page', function () {
    $admin = User::factory()->admin()->create();
    $other = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($admin->email)
        ->assertSee($other->email);
});

test('agents cannot change a user status', function () {
    $agent = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($agent)
        ->patch(route('admin.users.update', $target), ['is_enabled' => false])
        ->assertForbidden();

    expect($target->fresh()->is_enabled)->toBeTrue();
});

test('admins can disable a user', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $target), ['is_enabled' => false])
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh()->is_enabled)->toBeFalse();
});

test('admins can enable a disabled user', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->disabled()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $target), ['is_enabled' => true])
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh()->is_enabled)->toBeTrue();
});

test('an admin cannot disable their own account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $admin), ['is_enabled' => false])
        ->assertForbidden();

    expect($admin->fresh()->is_enabled)->toBeTrue();
    expect(User::query()->where('role', 'admin')->where('is_enabled', true)->count())->toBe(1);
});

test('an admin can disable another admin while an active admin remains', function () {
    $admin = User::factory()->admin()->create();
    $otherAdmin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $otherAdmin), ['is_enabled' => false])
        ->assertRedirect(route('admin.users.index'));

    expect($otherAdmin->fresh()->is_enabled)->toBeFalse();
});

test('disabling a user revokes their active sessions and remember token', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create();

    DB::table('sessions')->insert([
        'id' => 'test-session',
        'user_id' => $target->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
        'payload' => 'payload',
        'last_activity' => now()->getTimestamp(),
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $target), ['is_enabled' => false])
        ->assertRedirect(route('admin.users.index'));

    expect(DB::table('sessions')->where('user_id', $target->id)->exists())->toBeFalse();
    expect($target->fresh()->remember_token)->toBeNull();
});

test('the status update requires a boolean', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->patch(route('admin.users.update', $target), ['is_enabled' => 'not-a-boolean'])
        ->assertSessionHasErrors('is_enabled');

    expect($target->fresh()->is_enabled)->toBeTrue();
});
