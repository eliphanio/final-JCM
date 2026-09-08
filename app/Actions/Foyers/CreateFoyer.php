<?php

namespace App\Actions\Foyers;

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateFoyer
{
    /**
     * Create a new foyer and add the user as owner.
     */
    public function handle(User $user, string $name, bool $isPersonal = false): Foyer
    {
        return DB::transaction(function () use ($user, $name, $isPersonal) {
            $foyer = Foyer::create([
                'name' => $name,
                'is_personal' => $isPersonal,
            ]);

            $membership = $foyer->memberships()->create([
                'user_id' => $user->id,
                'role' => FoyerRole::Owner,
            ]);

            $user->switchFoyer($foyer);

            return $foyer;
        });
    }
}
