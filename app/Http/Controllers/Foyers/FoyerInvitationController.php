<?php

namespace App\Http\Controllers\Foyers;

use App\Enums\FoyerRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Foyers\CreateFoyerInvitationRequest;
use App\Http\Requests\Foyers\RespondToFoyerInvitationRequest;
use App\Models\Foyer;
use App\Models\FoyerInvitation;
use App\Notifications\Foyers\FoyerInvitation as FoyerInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class FoyerInvitationController extends Controller
{
    /**
     * Store a newly created invitation.
     */
    public function store(CreateFoyerInvitationRequest $request, Foyer $foyer): RedirectResponse
    {
        Gate::authorize('inviteMember', $foyer);

        $invitation = $foyer->invitations()->create([
            'email' => $request->validated('email'),
            'role' => FoyerRole::from($request->validated('role')),
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new FoyerInvitationNotification($invitation));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation sent.')]);

        return to_route('foyers.edit', ['foyer' => $foyer->slug]);
    }

    /**
     * Cancel the specified invitation.
     */
    public function destroy(Foyer $foyer, FoyerInvitation $invitation): RedirectResponse
    {
        abort_unless($invitation->foyer_id === $foyer->id, 404);

        Gate::authorize('cancelInvitation', $foyer);

        $invitation->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation cancelled.')]);

        return to_route('foyers.edit', ['foyer' => $foyer->slug]);
    }

    /**
     * Accept the invitation.
     */
    public function accept(RespondToFoyerInvitationRequest $request, FoyerInvitation $invitation): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($user, $invitation) {
            $foyer = $invitation->foyer;

            $foyer->memberships()->firstOrCreate(
                ['user_id' => $user->id],
                ['role' => $invitation->role],
            );

            $invitation->update(['accepted_at' => now()]);

            $user->switchFoyer($foyer);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation accepted.')]);

        return to_route('dashboard');
    }

    /**
     * Decline the invitation.
     */
    public function decline(RespondToFoyerInvitationRequest $request, FoyerInvitation $invitation): RedirectResponse
    {
        $invitation->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation declined.')]);

        return to_route('dashboard');
    }
}
