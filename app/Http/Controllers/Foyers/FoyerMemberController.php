<?php

namespace App\Http\Controllers\Foyers;

use App\Enums\FoyerRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Foyers\UpdateFoyerMemberRequest;
use App\Models\Membership;
use App\Models\Foyer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FoyerMemberController extends Controller
{
    /**
     * Update the specified foyer member's role.
     */

    public function layoutMember(Request $request, Foyer $current_foyer){

        $user = $request->user();

        return Inertia::render('collocataire', [
            'foyer' => [
                'id' => $current_foyer->id,
                'name' => $current_foyer->name,
                'slug' => $current_foyer->slug,
                'isPersonal' => $current_foyer->is_personal,
            ],
            'members' => $current_foyer->members()->get()->map(function (User $member) {
                /** @var Membership $membership */
                $membership = $member->getRelation('pivot');

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'avatar' => $member->avatar ?? null,
                    'role' => $membership->role->value,
                    'role_label' => $membership->role->label(),
                ];
            }),
            'invitations' => $current_foyer->invitations()
                ->whereNull('accepted_at')
                ->get()
                ->map(fn ($invitation) => [
                    'code' => $invitation->code,
                    'email' => $invitation->email,
                    'role' => $invitation->role->value,
                    'role_label' => $invitation->role->label(),
                    'created_at' => $invitation->created_at->toISOString(),
                ]),
            'permissions' => $user->toFoyerPermissions($current_foyer),
            'availableRoles' => FoyerRole::assignable(),
        ]);

    }

    public function update(UpdateFoyerMemberRequest $request, Foyer $foyer, User $user): RedirectResponse
    {
        Gate::authorize('updateMember', $foyer);

        $newRole = FoyerRole::from($request->validated('role'));

        $foyer->memberships()
            ->where('user_id', $user->id)
            ->firstOrFail()
            ->update(['role' => $newRole]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Member role updated.')]);

        return to_route('foyers.edit', ['foyer' => $foyer->slug]);
    }

    /**
     * Remove the specified foyer member.
     */
    public function destroy(Foyer $foyer, User $user): RedirectResponse
    {
        Gate::authorize('removeMember', $foyer);

        abort_if($foyer->owner()?->is($user), 403, __('The foyer owner cannot be removed.'));

        $foyer->memberships()
            ->where('user_id', $user->id)
            ->delete();

        if ($user->isCurrentFoyer($foyer)) {
            $user->switchFoyer($user->personalFoyer());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Member removed.')]);

        return to_route('foyers.edit', ['foyer' => $foyer->slug]);
    }
}
