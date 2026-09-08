<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetFoyerUrlDefaults
{
    /**
     * Set the default URL parameters for foyer-based routes.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($currentFoyer = $request->user()?->currentFoyer) {
            URL::defaults([
                'current_foyer' => $currentFoyer->slug,
                'foyer' => $currentFoyer->slug,
            ]);
        }

        return $next($request);
    }
}
