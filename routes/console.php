<?php

use App\Models\FoyerInvitation;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    FoyerInvitation::query()
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->delete();
})->daily()->description('Delete expired foyer invitations');
