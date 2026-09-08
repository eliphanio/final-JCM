<?php

namespace App\Http\Controllers\Foyers;

use App\Enums\FoyerRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Foyers\UpdateFoyerMemberRequest;
use App\Models\Foyer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FoyerMemberController extends Controller
{
    /**
     * Update the specified foyer member's role.
     */

    public function layoutMember(Foyer $foyer){

        return Inertia::render('collocataire');

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
