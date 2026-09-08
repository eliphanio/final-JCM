<?php

namespace App\Http\Responses\Concerns;

use App\Models\Foyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

trait RedirectsToCurrentFoyer
{
    protected function redirectPathForCurrentFoyer(Request $request, string $redirect): string
    {
        $foyer = $this->currentFoyer($request);

        URL::defaults(['current_foyer' => $foyer->slug]);

        return "/{$foyer->slug}{$redirect}";
    }

    protected function currentFoyer(Request $request): Foyer
    {
        $user = $request->user();

        abort_if(! $user, 403);

        $foyer = $user->currentFoyer ?? $user->personalFoyer();

        abort_if(! $foyer, 403);

        return $foyer;
    }
}
