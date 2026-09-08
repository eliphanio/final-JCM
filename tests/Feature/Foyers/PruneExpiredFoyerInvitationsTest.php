<?php

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\FoyerInvitation;
use App\Models\User;

test('expired invitations are deleted by the scheduled cleanup', function () {
    $this->travelTo(now()->startOfDay());

    $owner = User::factory()->create();
    $foyer = Foyer::factory()->create();

    $foyer->members()->attach($owner, ['role' => FoyerRole::Owner->value]);

    $expiredInvitation = FoyerInvitation::factory()->expired()->create([
        'foyer_id' => $foyer->id,
        'invited_by' => $owner->id,
    ]);

    $unexpiredInvitation = FoyerInvitation::factory()->expiresIn(1)->create([
        'foyer_id' => $foyer->id,
        'invited_by' => $owner->id,
    ]);

    $invitationWithoutExpiration = FoyerInvitation::factory()->create([
        'foyer_id' => $foyer->id,
        'invited_by' => $owner->id,
    ]);

    $this->artisan('schedule:run')->assertSuccessful();

    $this->assertDatabaseMissing('foyer_invitations', [
        'id' => $expiredInvitation->id,
    ]);

    $this->assertDatabaseHas('foyer_invitations', [
        'id' => $unexpiredInvitation->id,
    ]);

    $this->assertDatabaseHas('foyer_invitations', [
        'id' => $invitationWithoutExpiration->id,
    ]);
});
