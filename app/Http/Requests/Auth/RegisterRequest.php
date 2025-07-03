<?php

namespace App\Http\Requests\Auth;

use App\Rules\NoMaliciousContent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

/**
 * Request validation for AuthController::register.
 *
 * @enhanced by RequestValidationImprover
 */
class RegisterRequest extends FormRequest
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
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(config('security.authentication.password_min_length', 8))
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'terms' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least :min characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters',
            'password.letters' => 'Password must contain letters',
            'password.numbers' => 'Password must contain numbers',
            'password.symbols' => 'Password must contain symbols',
            'password.uncompromised' => 'The given password has appeared in a data breach. Please choose a different password.',
            'terms.required' => 'You must accept the terms and conditions',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email address',
            'phone' => 'phone number',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check for malicious content in text fields
            foreach (['first_name', 'last_name'] as $field) {
                if ($this->has($field) && $this->{$field}) {
                    $rule = new NoMaliciousContent;
                    if (! $rule->passes($field, $this->{$field})) {
                        $validator->errors()->add($field, $rule->message());
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        if ($this->has('first_name')) {
            $this->merge([
                'first_name' => strip_tags($this->first_name),
            ]);
        }

        if ($this->has('last_name')) {
            $this->merge([
                'last_name' => strip_tags($this->last_name),
            ]);
        }
    }
}
