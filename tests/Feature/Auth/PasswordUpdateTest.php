<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/profile');
});

test('password errors are visible in the password form without reopening account deletion', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->followingRedirects()->from(route('profile.edit'))
        ->put(route('password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSee('The password is incorrect.')
        ->assertSee('id="current-password-error"', false)
        ->assertDontSee('id="delete-account-password-error"', false)
        ->assertSee('x-data="{ open: false }"', false)
        ->assertDontSee('value="wrong-password"', false);

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('password success feedback is visible after a password change', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->followingRedirects()->from(route('profile.edit'))
        ->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSee('Password updated.');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
