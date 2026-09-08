<?php

namespace App\Http\Controllers;

use App\Models\FoyerInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $email = strtolower($request->user()->email);

        $pendingInvitations = FoyerInvitation::query()
            ->with(['inviter', 'foyer'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (FoyerInvitation $invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'foyer' => [
                    'name' => $invitation->foyer->name,
                    'slug' => $invitation->foyer->slug,
                ],
            ]);

        return Inertia::render('dashboard', [
            'pendingInvitations' => $pendingInvitations,
        ]);
    }
}
