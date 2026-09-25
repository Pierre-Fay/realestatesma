<?php

use App\Models\User;

test('administrators can access the administration area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin'))
        ->assertOk();
});

test('agents cannot access the administration area', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)
        ->get(route('admin'))
        ->assertForbidden();
});

test('dashboard shows the administrator role to admins', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('You are signed in as an Administrator.');
});

test('dashboard shows the agent role to agents', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('You are signed in as an Agent.')
        ->assertDontSee('Open administration');
});
