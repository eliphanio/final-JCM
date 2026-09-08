<?php

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\FoyerInvitation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $user = User::factory()->create();
    $foyer = $user->currentFoyer;

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $foyer = $user->currentFoyer;

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard includes pending invitations for the authenticated user', function () {
    $owner = User::factory()->create(['name' => 'Taylor Otwell']);
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create(['name' => 'Laravel Foyer']);

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('dashboard')
        ->has('pendingInvitations', 1)
        ->where('pendingInvitations.0.code', $invitation->code)
        ->where('pendingInvitations.0.inviterName', 'Taylor Otwell')
        ->where('pendingInvitations.0.foyer.name', 'Laravel Foyer')
        ->where('pendingInvitations.0.foyer.slug', $foyer->slug)
        ->missing('pendingInvitations.0.foyerName'),
    );
});

test('dashboard does not include accepted invitations', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    FoyerInvitation::factory()->accepted()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('dashboard')
        ->has('pendingInvitations', 0),
    );
});

test('dashboard excludes expired invitations without deleting them', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->expired()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('dashboard')
        ->has('pendingInvitations', 0),
    );

    $this->assertDatabaseHas('foyer_invitations', [
        'id' => $invitation->id,
    ]);
});

test('dashboard does not include or delete other users invitations', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->expired()->create([
        'foyer_id' => $foyer->id,
        'email' => 'someone@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('dashboard')
        ->has('pendingInvitations', 0),
    );

    $this->assertDatabaseHas('foyer_invitations', [
        'id' => $invitation->id,
    ]);
});
