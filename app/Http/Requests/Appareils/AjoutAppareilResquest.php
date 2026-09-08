<?php

namespace App\Http\Requests\Appareils;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AjoutAppareilResquest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'power_watt' => (int) $this->power_watt,
            'usage' => (int) $this->usage,
        ]);
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
            "name" => ["required", "string"],
            "power_watt" => ["required", "integer"],
            "owner" => ["required", "email"],
            "usage" => ["required", "integer"],
        ];
    }
}
