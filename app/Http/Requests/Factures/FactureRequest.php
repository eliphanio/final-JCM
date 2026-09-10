<?php

namespace App\Http\Requests\Factures;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FactureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'eau' => ['required', 'numeric'],
            'electricite' => ['required', 'numeric'],
            'periode' => ['required'],
            'consomation' => ['required'],
            'due_date' => ['required']
        ];
    }
}
