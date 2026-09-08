<?php

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\User;

test('foyer member roles can be updated by owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($owner)
        ->patch(route('foyers.members.update', [$foyer, $member]), [
            'role' => FoyerRole::Admin->value,
        ]);

    $response->assertRedirect(route('foyers.edit', $foyer));

    expect($foyer->members()->where('user_id', $member->id)->first()->pivot->role->value)->toEqual(FoyerRole::Admin->value);
});

test('foyer member roles cannot be updated by non owners', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($admin, ['role' => FoyerRole::Admin->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($admin)
        ->patch(route('foyers.members.update', [$foyer, $member]), [
            'role' => FoyerRole::Admin->value,
        ]);

    $response->assertForbidden();
});

test('foyer members can be removed by owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($owner)
        ->delete(route('foyers.members.destroy', [$foyer, $member]));

    $response->assertRedirect(route('foyers.edit', $foyer));

    expect($member->fresh()->belongsToFoyer($foyer))->toBeFalse();
});

test('foyer members cannot be removed by non owners', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($admin, ['role' => FoyerRole::Admin->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('foyers.members.destroy', [$foyer, $member]));

    $response->assertForbidden();
});

test('foyer owner cannot be removed', function () {
    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($owner)
        ->delete(route('foyers.members.destroy', [$foyer, $owner]));

    $response->assertForbidden();

    expect($owner->fresh()->belongsToFoyer($foyer))->toBeTrue();
});

test('foyer member role cannot be set to owner', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($owner)
        ->patch(route('foyers.members.update', [$foyer, $member]), [
            'role' => FoyerRole::Owner->value,
        ]);

    $response->assertSessionHasErrors('role');

    expect($foyer->members()->where('user_id', $member->id)->first()->pivot->role->value)->toEqual(FoyerRole::Member->value);
});

test('removed member current foyer is set to personal foyer', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $personalFoyer = $member->personalFoyer();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $member->update(['current_foyer_id' => $foyer->id]);

    $this
        ->actingAs($owner)
        ->delete(route('foyers.members.destroy', [$foyer, $member]));

    expect($member->fresh()->current_foyer_id)->toEqual($personalFoyer->id);
});
