<?php

namespace App\Http\Requests\Foyers;

use App\Enums\FoyerRole;
use App\Models\Foyer;
use App\Rules\UniqueFoyerInvitation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFoyerInvitationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $foyer = $this->route('foyer');

        abort_if(! $foyer instanceof Foyer, 404);

        return [
            'email' => ['required', 'string', 'email', 'max:255', new UniqueFoyerInvitation($foyer)],
            'role' => ['required', 'string', Rule::enum(FoyerRole::class)],
        ];
    }
}
