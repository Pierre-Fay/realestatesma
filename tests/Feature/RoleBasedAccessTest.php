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
        ->assertSee('You are signed in as an Administrator.')
        ->assertSeeInOrder([
            'Quick access',
            route('admin.leads.index'),
            route('admin.properties.index'),
            route('admin.agents.index'),
            route('admin.users.index'),
            'href="'.route('admin').'"',
            route('profile.edit'),
        ], false)
        ->assertDontSee([route('leads.index'), route('listings.index'), route('listings.create')]);
});

test('dashboard shows the agent role to agents', function () {
    $agent = User::factory()->create();

    $this->actingAs($agent)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('You are signed in as an Agent.')
        ->assertSeeInOrder([
            'Quick access',
            route('leads.index'),
            route('listings.index'),
            route('listings.create'),
            route('profile.edit'),
        ])
        ->assertDontSee([
            'Open administration',
            route('admin.leads.index'),
            route('admin.users.index'),
            route('admin.agents.index'),
            route('admin.properties.index'),
        ]);
});
