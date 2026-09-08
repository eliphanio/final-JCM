<?php

namespace App\Http\Responses;

use App\Http\Responses\Concerns\RedirectsToCurrentFoyer;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    use RedirectsToCurrentFoyer;

    public function toResponse($request): Response
    {
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended($this->redirectPathForCurrentFoyer($request, Fortify::redirects('email-verification')).'?verified=1');
    }
}
