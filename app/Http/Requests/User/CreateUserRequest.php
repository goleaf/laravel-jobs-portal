<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => ['required', 'confirmed', Password::min(8)->uncompromised()],
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'marital_status_id' => 'nullable|integer|exists:marital_status,id',
            'nationality_id' => 'nullable|integer|exists:countries,id',
            'country_id' => 'nullable|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'language' => 'nullable|string|in:en,ar,de,es,fr,pt,ru,tr,zh',
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'role' => 'required|string|in:admin,employer,candidate',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => __('validation.user.first_name.required'),
            'first_name.string' => __('validation.user.first_name.string'),
            'first_name.max' => __('validation.user.first_name.max'),
            'last_name.required' => __('validation.user.last_name.required'),
            'last_name.string' => __('validation.user.last_name.string'),
            'last_name.max' => __('validation.user.last_name.max'),
            'email.required' => __('validation.user.email.required'),
            'email.email' => __('validation.user.email.email'),
            'email.unique' => __('validation.user.email.unique'),
            'email.max' => __('validation.user.email.max'),
            'password.required' => __('validation.user.password.required'),
            'password.confirmed' => __('validation.user.password.confirmed'),
            'phone.string' => __('validation.user.phone.string'),
            'phone.max' => __('validation.user.phone.max'),
            'dob.date' => __('validation.user.dob.date'),
            'dob.before' => __('validation.user.dob.before'),
            'gender.in' => __('validation.user.gender.in'),
            'marital_status_id.exists' => __('validation.user.marital_status_id.exists'),
            'nationality_id.exists' => __('validation.user.nationality_id.exists'),
            'country_id.exists' => __('validation.user.country_id.exists'),
            'state_id.exists' => __('validation.user.state_id.exists'),
            'city_id.exists' => __('validation.user.city_id.exists'),
            'language.in' => __('validation.user.language.in'),
            'role.required' => __('validation.user.role.required'),
            'role.in' => __('validation.user.role.in'),
            'avatar.image' => __('validation.user.avatar.image'),
            'avatar.mimes' => __('validation.user.avatar.mimes'),
            'avatar.max' => __('validation.user.avatar.max'),
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
            'first_name' => __('attributes.user.first_name'),
            'last_name' => __('attributes.user.last_name'),
            'email' => __('attributes.user.email'),
            'password' => __('attributes.user.password'),
            'phone' => __('attributes.user.phone'),
            'dob' => __('attributes.user.date_of_birth'),
            'gender' => __('attributes.user.gender'),
            'marital_status_id' => __('attributes.user.marital_status'),
            'nationality_id' => __('attributes.user.nationality'),
            'country_id' => __('attributes.user.country'),
            'state_id' => __('attributes.user.state'),
            'city_id' => __('attributes.user.city'),
            'language' => __('attributes.user.language'),
            'role' => __('attributes.user.role'),
            'avatar' => __('attributes.user.avatar'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'is_verified' => $this->boolean('is_verified', false),
            'language' => $this->input('language', app()->getLocale()),
        ]);

        // Clean phone number
        if ($this->has('phone')) {
            $phone = preg_replace('/[^0-9+\-\s]/', '', $this->input('phone'));
            $this->merge(['phone' => $phone]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log user creation attempt for security
        \Log::info('User creation attempted', [
            'admin_user_id' => $this->user()->id,
            'email' => $this->input('email'),
            'role' => $this->input('role'),
            'ip' => $this->ip(),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate age requirement for certain roles
            if ($this->input('dob') && $this->input('role') === 'candidate') {
                $age = \Carbon\Carbon::parse($this->input('dob'))->age;
                if ($age < 16) {
                    $validator->errors()->add('dob', __('validation.user.dob.minimum_age'));
                }
            }

            // Validate location hierarchy
            if ($this->input('state_id') && $this->input('country_id')) {
                $state = \App\Models\State::find($this->input('state_id'));
                if ($state && $state->country_id != $this->input('country_id')) {
                    $validator->errors()->add('state_id', __('validation.user.state_country_mismatch'));
                }
            }

            if ($this->input('city_id') && $this->input('state_id')) {
                $city = \App\Models\City::find($this->input('city_id'));
                if ($city && $city->state_id != $this->input('state_id')) {
                    $validator->errors()->add('city_id', __('validation.user.city_state_mismatch'));
                }
            }
        });
    }
} 