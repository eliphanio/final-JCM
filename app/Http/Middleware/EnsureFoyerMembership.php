<?php

namespace App\Http\Middleware;

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFoyerMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $minimumRole = null): Response
    {
        [$user, $foyer] = [$request->user(), $this->foyer($request)];

        abort_if(! $user || ! $foyer || ! $user->belongsToFoyer($foyer), 403);

        $this->ensureFoyerMemberHasRequiredRole($user, $foyer, $minimumRole);

        if ($request->route('current_foyer') && ! $user->isCurrentFoyer($foyer)) {
            $user->switchFoyer($foyer);
        }

        return $next($request);
    }

    /**
     * Ensure the given user has at least the given role, if applicable.
     */
    protected function ensureFoyerMemberHasRequiredRole(User $user, Foyer $foyer, ?string $minimumRole): void
    {
        if ($minimumRole === null) {
            return;
        }

        $role = $user->foyerRole($foyer);

        $requiredRole = FoyerRole::tryFrom($minimumRole);

        abort_if(
            $requiredRole === null ||
            $role === null ||
            ! $role->isAtLeast($requiredRole),
            403,
        );
    }

    /**
     * Get the foyer associated with the request.
     */
    protected function foyer(Request $request): ?Foyer
    {
        $foyer = $request->route('current_foyer') ?? $request->route('foyer');

        if (is_string($foyer)) {
            $foyer = Foyer::where('slug', $foyer)->first();
        }

        return $foyer;
    }
}
