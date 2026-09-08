<?php

namespace App\Http\Responses;

use App\Http\Responses\Concerns\RedirectsToCurrentFoyer;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Fortify;
use Laravel\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasskeyLoginResponse implements PasskeyLoginResponseContract
{
    use RedirectsToCurrentFoyer;

    public function toResponse($request): Response
    {
        $redirect = $this->redirectPathForCurrentFoyer($request, Fortify::redirects('login'));

        return $request->wantsJson()
            ? new JsonResponse(['redirect' => redirect()->intended($redirect)->getTargetUrl()], 200)
            : redirect()->intended($redirect);
    }
}
