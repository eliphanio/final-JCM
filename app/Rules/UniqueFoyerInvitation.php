<?php

namespace App\Rules;

use App\Models\Foyer;
use App\Models\FoyerInvitation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueFoyerInvitation implements ValidationRule
{
    public function __construct(protected Foyer $foyer)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower($value);

        $isMember = $this->foyer->members()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if ($isMember) {
            $fail(__('This user is already a member of the foyer.'));

            return;
        }

        $hasPendingInvitation = FoyerInvitation::where('foyer_id', $this->foyer->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasPendingInvitation) {
            $fail(__('An invitation has already been sent to this email address.'));
        }
    }
}
