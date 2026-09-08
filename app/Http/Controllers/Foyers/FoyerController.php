<?php

namespace App\Http\Controllers\Foyers;

use App\Actions\Foyers\CreateFoyer;
use App\Enums\FoyerRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Foyers\DeleteFoyerRequest;
use App\Http\Requests\Foyers\SaveFoyerRequest;
use App\Models\Foyer;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class FoyerController extends Controller
{
    /**
     * Display a listing of the user's foyers.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('foyers/index', [
            'foyers' => $user->toUserFoyers(includeCurrent: true),
        ]);
    }

    /**
     * Store a newly created foyer.
     */
    public function store(SaveFoyerRequest $request, CreateFoyer $createFoyer): RedirectResponse
    {
        $foyer = $createFoyer->handle($request->user(), $request->validated('name'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Foyer created.')]);

        return to_route('foyers.edit', ['foyer' => $foyer->slug]);
    }

    /**
     * Show the foyer edit page.
     */
    public function edit(Request $request, Foyer $foyer): Response
    {
        $user = $request->user();

        return Inertia::render('foyers/edit', [
            'foyer' => [
                'id' => $foyer->id,
                'name' => $foyer->name,
                'slug' => $foyer->slug,
                'isPersonal' => $foyer->is_personal,
            ],
            'members' => $foyer->members()->get()->map(function (User $member) {
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
            'invitations' => $foyer->invitations()
                ->whereNull('accepted_at')
                ->get()
                ->map(fn ($invitation) => [
                    'code' => $invitation->code,
                    'email' => $invitation->email,
                    'role' => $invitation->role->value,
                    'role_label' => $invitation->role->label(),
                    'created_at' => $invitation->created_at->toISOString(),
                ]),
            'permissions' => $user->toFoyerPermissions($foyer),
            'availableRoles' => FoyerRole::assignable(),
        ]);
    }

    /**
     * Update the specified foyer.
     */
    public function update(SaveFoyerRequest $request, Foyer $foyer): RedirectResponse
    {
        Gate::authorize('update', $foyer);

        $foyer = DB::transaction(function () use ($request, $foyer) {
            $foyer = Foyer::whereKey($foyer->id)->lockForUpdate()->firstOrFail();

            $foyer->update(['name' => $request->validated('name')]);

            return $foyer;
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Foyer updated.')]);

        return to_route('foyers.edit', ['foyer' => $foyer->slug]);
    }

    /**
     * Switch the user's current foyer.
     */
    public function switch(Request $request, Foyer $foyer): RedirectResponse
    {
        abort_unless($request->user()->belongsToFoyer($foyer), 403);

        $request->user()->switchFoyer($foyer);

        return back();
    }

    /**
     * Leave the specified foyer.
     */
    public function leave(Request $request, Foyer $foyer): RedirectResponse
    {
        Gate::authorize('leave', $foyer);

        $user = $request->user();

        $fallbackFoyer = $user->isCurrentFoyer($foyer)
            ? $user->fallbackFoyer($foyer)
            : null;

        $foyer->memberships()
            ->where('user_id', $user->id)
            ->delete();

        if ($fallbackFoyer) {
            $user->switchFoyer($fallbackFoyer);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('You left the foyer ":name"', ['name' => $foyer->name])]);

        return to_route('foyers.index');
    }

    /**
     * Delete the specified foyer.
     */
    public function destroy(DeleteFoyerRequest $request, Foyer $foyer): RedirectResponse
    {
        $user = $request->user();
        $fallbackFoyer = $user->isCurrentFoyer($foyer)
            ? $user->fallbackFoyer($foyer)
            : null;

        DB::transaction(function () use ($user, $foyer) {
            User::where('current_foyer_id', $foyer->id)
                ->where('id', '!=', $user->id)
                ->each(fn (User $affectedUser) => $affectedUser->switchFoyer($affectedUser->personalFoyer()));

            $foyer->invitations()->delete();
            $foyer->memberships()->delete();
            $foyer->delete();
        });

        if ($fallbackFoyer) {
            $user->switchFoyer($fallbackFoyer);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Foyer deleted.')]);

        return to_route('foyers.index');
    }
}
