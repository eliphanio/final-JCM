<?php

namespace App\Http\Requests\Foyers;

use App\Models\Foyer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class DeleteFoyerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('delete', $this->route('foyer'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @return array<int, Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('name') !== $this->foyer()->name) {
                    $validator->errors()->add('name', __('The foyer name does not match.'));
                }
            },
        ];
    }

    /**
     * Get the foyer associated with the request.
     */
    private function foyer(): Foyer
    {
        $foyer = $this->route('foyer');

        abort_if(! $foyer instanceof Foyer, 404);

        return $foyer;
    }
}
