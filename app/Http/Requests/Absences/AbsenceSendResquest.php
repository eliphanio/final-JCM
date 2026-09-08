<?php

namespace App\Http\Requests\Absences;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AbsenceSendResquest extends FormRequest
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
            "start_day" => ['required', 'date', 'after_or_equal:today'],
            "end_day" => ['required', 'date', 'after_or_equal:start_day']
        ];
    }
}
