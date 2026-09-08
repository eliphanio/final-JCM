<?php

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('the foyers index page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('foyers.index'));

    $response->assertOk();
});

test('foyers can be created', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('foyers.store'), [
            'name' => 'Test Foyer',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('foyers', [
        'name' => 'Test Foyer',
        'is_personal' => false,
    ]);
});

test('personal foyer returns the foyer owned by the user', function () {
    $otherUser = User::factory()->create();
    $user = User::factory()->make();
    $user->save();

    $otherUser->personalFoyer()->members()->attach($user, [
        'role' => FoyerRole::Member->value,
    ]);

    $personalFoyer = Foyer::factory()->personal()->create();
    $personalFoyer->members()->attach($user, [
        'role' => FoyerRole::Owner->value,
    ]);

    expect($personalFoyer->is($user->personalFoyer()))->toBeTrue();
});

test('foyer slug uses next available suffix', function () {
    $user = User::factory()->create();

    Foyer::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
    Foyer::factory()->create(['name' => 'Acme One', 'slug' => 'acme-1']);
    Foyer::factory()->create(['name' => 'Acme Ten', 'slug' => 'acme-10']);

    $this
        ->actingAs($user)
        ->post(route('foyers.store'), [
            'name' => 'Acme',
        ]);

    $this->assertDatabaseHas('foyers', [
        'name' => 'Acme',
        'slug' => 'acme-11',
    ]);
});

test('the foyer edit page can be rendered', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($user)
        ->get(route('foyers.edit', $foyer));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('foyers/edit')
            ->where('members.0.role', FoyerRole::Owner->value)
            ->where('members.0.role_label', FoyerRole::Owner->label()),
        );
});

test('foyers can be updated by owners', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create(['name' => 'Original Name']);

    $foyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($user)
        ->patch(route('foyers.update', $foyer), [
            'name' => 'Updated Name',
        ]);

    $response->assertRedirect(route('foyers.edit', $foyer->fresh()));

    $this->assertDatabaseHas('foyers', [
        'id' => $foyer->id,
        'name' => 'Updated Name',
    ]);
});

test('foyers cannot be updated by members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($member)
        ->patch(route('foyers.update', $foyer), [
            'name' => 'Updated Name',
        ]);

    $response->assertForbidden();
});

test('foyers can be deleted by owners', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.destroy', $foyer), [
            'name' => $foyer->name,
        ]);

    $response->assertRedirect();

    $this->assertSoftDeleted('foyers', [
        'id' => $foyer->id,
    ]);
});

test('foyer deletion requires name confirmation', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.destroy', $foyer), [
            'name' => 'Wrong Name',
        ]);

    $response->assertSessionHasErrors('name');

    $this->assertDatabaseHas('foyers', [
        'id' => $foyer->id,
        'deleted_at' => null,
    ]);
});

test('deleting current foyer switches to alphabetically first remaining foyer', function () {
    $user = User::factory()->create(['name' => 'Mike']);

    $zuluFoyer = Foyer::factory()->create(['name' => 'Zulu Foyer']);
    $zuluFoyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $alphaFoyer = Foyer::factory()->create(['name' => 'Alpha Foyer']);
    $alphaFoyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $betaFoyer = Foyer::factory()->create(['name' => 'Beta Foyer']);
    $betaFoyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $user->update(['current_foyer_id' => $zuluFoyer->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.destroy', $zuluFoyer), [
            'name' => $zuluFoyer->name,
        ]);

    $response->assertRedirect();

    $this->assertSoftDeleted('foyers', [
        'id' => $zuluFoyer->id,
    ]);

    expect($user->fresh()->current_foyer_id)->toEqual($alphaFoyer->id);
});

test('deleting current foyer falls back to personal foyer when alphabetically first', function () {
    $user = User::factory()->create();
    $personalFoyer = $user->personalFoyer();
    $foyer = Foyer::factory()->create(['name' => 'Zulu Foyer']);
    $foyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $user->update(['current_foyer_id' => $foyer->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.destroy', $foyer), [
            'name' => $foyer->name,
        ]);

    $response->assertRedirect();

    $this->assertSoftDeleted('foyers', [
        'id' => $foyer->id,
    ]);

    expect($user->fresh()->current_foyer_id)->toEqual($personalFoyer->id);
});

test('deleting non current foyer leaves current foyer unchanged', function () {
    $user = User::factory()->create();
    $personalFoyer = $user->personalFoyer();
    $foyer = Foyer::factory()->create();
    $foyer->members()->attach($user, ['role' => FoyerRole::Owner->value]);

    $user->update(['current_foyer_id' => $personalFoyer->id]);

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.destroy', $foyer), [
            'name' => $foyer->name,
        ]);

    $response->assertRedirect();

    $this->assertSoftDeleted('foyers', [
        'id' => $foyer->id,
    ]);

    expect($user->fresh()->current_foyer_id)->toEqual($personalFoyer->id);
});

test('members can leave non personal foyers', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($member)
        ->delete(route('foyers.leave', $foyer));

    $response->assertRedirect(route('foyers.index'));
    $response->assertInertiaFlash('toast', ['type' => 'success', 'message' => "You left the foyer \"{$foyer->name}\""]);

    expect($member->fresh()->belongsToFoyer($foyer))->toBeFalse();
});

test('leaving current foyer switches to alphabetically first remaining foyer', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create(['name' => 'Mike']);

    $zuluFoyer = Foyer::factory()->create(['name' => 'Zulu Foyer']);
    $zuluFoyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $zuluFoyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $alphaFoyer = Foyer::factory()->create(['name' => 'Alpha Foyer']);
    $alphaFoyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $betaFoyer = Foyer::factory()->create(['name' => 'Beta Foyer']);
    $betaFoyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $member->update(['current_foyer_id' => $zuluFoyer->id]);

    $response = $this
        ->actingAs($member)
        ->delete(route('foyers.leave', $zuluFoyer));

    $response->assertRedirect(route('foyers.index'));

    expect($member->fresh()->belongsToFoyer($zuluFoyer))->toBeFalse();
    expect($member->fresh()->current_foyer_id)->toEqual($alphaFoyer->id);
});

test('personal foyers cannot be left', function () {
    $user = User::factory()->create();
    $personalFoyer = $user->personalFoyer();

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.leave', $personalFoyer));

    $response->assertForbidden();

    expect($user->fresh()->belongsToFoyer($personalFoyer))->toBeTrue();
});

test('foyer owners cannot leave their foyer', function () {
    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $response = $this
        ->actingAs($owner)
        ->delete(route('foyers.leave', $foyer));

    $response->assertForbidden();

    expect($owner->fresh()->belongsToFoyer($foyer))->toBeTrue();
});

test('users cannot leave foyers they dont belong to', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.leave', $foyer));

    $response->assertForbidden();
});

test('deleting foyer switches other affected users to their personal foyer', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $foyer = Foyer::factory()->create();
    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $owner->update(['current_foyer_id' => $foyer->id]);
    $member->update(['current_foyer_id' => $foyer->id]);

    $response = $this
        ->actingAs($owner)
        ->delete(route('foyers.destroy', $foyer), [
            'name' => $foyer->name,
        ]);

    $response->assertRedirect();

    expect($member->fresh()->current_foyer_id)->toEqual($member->personalFoyer()->id);
});

test('personal foyers cannot be deleted', function () {
    $user = User::factory()->create();

    $personalFoyer = $user->personalFoyer();

    $response = $this
        ->actingAs($user)
        ->delete(route('foyers.destroy', $personalFoyer), [
            'name' => $personalFoyer->name,
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('foyers', [
        'id' => $personalFoyer->id,
        'deleted_at' => null,
    ]);
});

test('foyers cannot be deleted by non owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);
    $foyer->members()->attach($member, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($member)
        ->delete(route('foyers.destroy', $foyer), [
            'name' => $foyer->name,
        ]);

    $response->assertForbidden();
});

test('users can switch foyers', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($user, ['role' => FoyerRole::Member->value]);

    $response = $this
        ->actingAs($user)
        ->post(route('foyers.switch', $foyer));

    $response->assertRedirect();

    expect($user->fresh()->current_foyer_id)->toEqual($foyer->id);
});

test('users cannot switch to foyer they dont belong to', function () {
    $user = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('foyers.switch', $foyer));

    $response->assertForbidden();
});

test('guests cannot access foyers', function () {
    $response = $this->get(route('foyers.index'));

    $response->assertRedirect(route('login'));
});
