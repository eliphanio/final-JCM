<?php

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\FoyerInvitation;
use App\Models\User;
use App\Notifications\Foyers\FoyerInvitation as FoyerInvitationNotification;
use Illuminate\Support\Facades\Notification;

test('foyer invitations can be created', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($owner)
        ->post(route('foyers.invitations.store', $foyer), [
            'email' => 'invited@example.com',
            'role' => FoyerRole::Member->value,
        ]);

    $response->assertRedirect(route('foyers.edit', $foyer));

    $this->assertDatabaseHas('foyer_invitations', [
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'role' => FoyerRole::Member->value,
    ]);
});

test('invitation email for existing users uses login route', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => $invitedUser->email,
        'invited_by' => $owner->id,
    ]);

    $mail = (new FoyerInvitationNotification($invitation))->toMail($invitedUser);

    expect($mail->actionUrl)->toBe(route('login', ['invitation' => $invitation->code]));
    $this->assertStringContainsString('dashboard', implode(' ', $mail->introLines));
});

test('invitation email for unknown users uses login route', function () {
    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'unknown@example.com',
        'invited_by' => $owner->id,
    ]);

    $mail = (new FoyerInvitationNotification($invitation))->toMail((object) []);

    expect($mail->actionUrl)->toBe(route('login', ['invitation' => $invitation->code]));
    $this->assertStringContainsString('log in', strtolower(implode(' ', $mail->introLines)));
});

test('foyer invitations can be created by admins', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($admin, ['role' => FoyerRole::Admin->value]);

    $response = $this
        ->actingAs($admin)
        ->post(route('foyers.invitations.store', $foyer), [
            'email' => 'invited@example.com',
            'role' => FoyerRole::Member->value,
        ]);

    $response->assertRedirect(route('foyers.edit', $foyer));
});

test('existing foyer members cannot be invited', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $member = User::factory()->create(['email' => 'member@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($owner)
        ->post(route('foyers.invitations.store', $foyer), [
            'email' => 'member@example.com',
            'role' => FoyerRole::Member->value,
        ]);

    $response->assertSessionHasErrors('email');
});

test('duplicate invitations cannot be created', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();
    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($owner)
        ->post(route('foyers.invitations.store', $foyer), [
            'email' => 'invited@example.com',
            'role' => FoyerRole::Member->value,
        ]);

    $response->assertSessionHasErrors('email');
});

test('foyer invitations cannot be created by members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($member)
        ->post(route('foyers.invitations.store', $foyer), [
            'email' => 'invited@example.com',
            'role' => FoyerRole::Member->value,
        ]);

    $response->assertForbidden();
});

test('foyer invitations can be cancelled by owners', function () {
    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($owner)
        ->delete(route('foyers.invitations.destroy', [$foyer, $invitation]));

    $response->assertRedirect(route('foyers.edit', $foyer));

    $this->assertDatabaseMissing('foyer_invitations', [
        'id' => $invitation->id,
    ]);
});

test('foyer invitations can be accepted', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'role' => FoyerRole::Member,
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->post(route('invitations.accept', $invitation));

    $response->assertRedirect(route('dashboard'));
    $response->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Invitation accepted.']);

    expect($invitedUser->fresh()->belongsToFoyer($foyer))->toBeTrue();
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('foyer invitations can be declined by the invited user', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->delete(route('invitations.decline', $invitation));

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseMissing('foyer_invitations', [
        'id' => $invitation->id,
    ]);
});

test('foyer invitations cannot be declined by uninvited user', function () {
    $owner = User::factory()->create();
    $uninvitedUser = User::factory()->create(['email' => 'uninvited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($uninvitedUser)
        ->delete(route('invitations.decline', $invitation));

    $response->assertSessionHasErrors('invitation');

    $this->assertDatabaseHas('foyer_invitations', [
        'id' => $invitation->id,
    ]);
});

test('accepted foyer invitations cannot be declined', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->accepted()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($invitedUser)
        ->delete(route('invitations.decline', $invitation));

    $response->assertSessionHasErrors('invitation');

    $this->assertDatabaseHas('foyer_invitations', [
        'id' => $invitation->id,
    ]);
});

test('foyer invitations cannot be accepted by uninvited user', function () {
    $owner = User::factory()->create();
    $uninvitedUser = User::factory()->create(['email' => 'uninvited@example.com']);
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $invitation = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $response = $this
        ->actingAs($uninvitedUser)
        ->post(route('invitations.accept', $invitation));

    $response->assertSessionHasErrors('invitation');

    expect($uninvitedUser->fresh()->belongsToFoyer($foyer))->toBeFalse();
});

test('expired invitations cannot be accepted', function () {
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
        ->post(route('invitations.accept', $invitation));

    $response->assertSessionHasErrors('invitation');

    expect($invitedUser->fresh()->belongsToFoyer($foyer))->toBeFalse();
});
