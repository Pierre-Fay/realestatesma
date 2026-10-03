<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('administrators cannot delete their own account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'delete')
        ->assertRedirect('/profile');

    $this->assertModelExists($admin);
    $this->assertAuthenticatedAs($admin);
});

test('account validation errors and submitted email are visible after redirect', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->followingRedirects()->from(route('profile.edit'))
        ->patch(route('profile.update'), ['name' => '', 'email' => 'invalid-email'])
        ->assertSee('The name field is required.')
        ->assertSee('The email field must be a valid email address.')
        ->assertSee('value="invalid-email"', false);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
});

test('failed account deletion reopens the confirmation with its field error', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->followingRedirects()->from(route('profile.edit'))
        ->delete(route('profile.destroy'), ['password' => 'wrong-password'])
        ->assertSee('The password is incorrect.')
        ->assertSee('id="delete-account-password-error"', false)
        ->assertSee('x-data="{ open: true }"', false)
        ->assertDontSee('id="new-password-error"', false)
        ->assertDontSee('value="wrong-password"', false);

    $this->assertModelExists($user);
    $this->assertAuthenticatedAs($user);
});

test('profile success feedback is visible after saving account information', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->followingRedirects()->from(route('profile.edit'))
        ->patch(route('profile.update'), ['name' => 'Updated Name', 'email' => $user->email])
        ->assertSee('Account information saved.')
        ->assertSee('value="Updated Name"', false);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
});
