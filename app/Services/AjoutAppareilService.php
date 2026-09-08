<?php

namespace App\Services;

use App\Models\Appareil;
use App\Models\Foyer;
use App\Models\User;

class AjoutAppareilService
{
    public function AjoutAppareil(array $device, Foyer $foyer)
    {
        $device['foyer_id'] = $foyer->id;

        $owner = $device['owner'];

        $device['user_id'] = User::where('email', $owner)->value('id');

        // dd($device);


        $isMember = $foyer->members()->where('user_id', $device['user_id'])->exists();

        if (!$isMember) {
            return back()->withErrors(['message' => 'Membre introuvable']);
        }


        Appareil::create($device);
    }
}
