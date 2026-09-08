<?php

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\FoyerInvitation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('registration screen includes foyer invitation context', function () {
    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create(['name' => 'Laravel Foyer']);
    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this->get(route('register', ['invitation' => $invitation->code]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('auth/register')
        ->where('foyerInvitation.code', $invitation->code)
        ->where('foyerInvitation.foyerName', 'Laravel Foyer'),
    );
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    $response->assertRedirect(route('dashboard'));
});
