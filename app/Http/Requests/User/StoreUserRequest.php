<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow registration for guests and admin user creation
        return !$this->user() || $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:180'],
            'last_name' => ['required', 'string', 'max:180'],
            'email' => ['required', 'string', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => ['required', 'integer', Rule::in([User::CANDIDATE, User::EMPLOYER])],
            'dob' => ['nullable', 'date', 'before:18 years ago', 'after:100 years ago'],
            'gender' => ['nullable', 'integer', Rule::in([0, 1, 2])], // 0=Male, 1=Female, 2=Other
            'phone' => ['nullable', 'string', 'max:20'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'facebook_url' => ['nullable', 'url', 'max:180'],
            'twitter_url' => ['nullable', 'url', 'max:180'],
            'linkedin_url' => ['nullable', 'url', 'max:180'],
            'google_plus_url' => ['nullable', 'url', 'max:180'],
            'pinterest_url' => ['nullable', 'url', 'max:180'],
            'language' => ['nullable', 'string', 'max:10'],
            'region_code' => ['nullable', 'string', 'max:10'],
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
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
            'user_type' => 'account type',
            'dob' => 'date of birth',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'facebook_url' => 'Facebook URL',
            'twitter_url' => 'Twitter URL',
            'linkedin_url' => 'LinkedIn URL',
            'google_plus_url' => 'Google+ URL',
            'pinterest_url' => 'Pinterest URL',
            'terms_accepted' => 'terms and conditions',
            'privacy_accepted' => 'privacy policy',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'The password confirmation does not match.',
            'user_type.required' => 'Please select an account type.',
            'user_type.in' => 'Please select a valid account type (Candidate or Employer).',
            'dob.before' => 'You must be at least 18 years old to register.',
            'dob.after' => 'Please enter a valid date of birth.',
            'gender.in' => 'Please select a valid gender option.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
            'privacy_accepted.accepted' => 'You must accept the privacy policy.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for state/city based on country
            if ($this->filled('state_id') && $this->filled('country_id')) {
                $stateExists = \App\Models\State::where('id', $this->input('state_id'))
                    ->where('country_id', $this->input('country_id'))
                    ->exists();
                
                if (!$stateExists) {
                    $validator->errors()->add('state_id', 'The selected state does not belong to the selected country.');
                }
            }

            if ($this->filled('city_id') && $this->filled('state_id')) {
                $cityExists = \App\Models\City::where('id', $this->input('city_id'))
                    ->where('state_id', $this->input('state_id'))
                    ->exists();
                
                if (!$cityExists) {
                    $validator->errors()->add('city_id', 'The selected city does not belong to the selected state.');
                }
            }

            // Validate social media URLs format
            $socialFields = ['facebook_url', 'twitter_url', 'linkedin_url', 'google_plus_url', 'pinterest_url'];
            foreach ($socialFields as $field) {
                if ($this->filled($field)) {
                    $url = $this->input($field);
                    $domain = parse_url($url, PHP_URL_HOST);
                    
                    $expectedDomains = [
                        'facebook_url' => ['facebook.com', 'www.facebook.com', 'fb.com'],
                        'twitter_url' => ['twitter.com', 'www.twitter.com', 'x.com', 'www.x.com'],
                        'linkedin_url' => ['linkedin.com', 'www.linkedin.com'],
                        'google_plus_url' => ['plus.google.com', 'www.plus.google.com'],
                        'pinterest_url' => ['pinterest.com', 'www.pinterest.com'],
                    ];

                    if (!in_array($domain, $expectedDomains[$field])) {
                        $platformName = str_replace('_url', '', $field);
                        $validator->errors()->add($field, "Please enter a valid {$platformName} URL.");
                    }
                }
            }
        });
    }

    /**
     * Handle a passed validation attempt.
     *
     * @return void
     */
    protected function passedValidation(): void
    {
        // Set default values
        $this->merge([
            'is_active' => true,
            'is_verified' => false,
            'profile_views' => 0,
            'language' => $this->input('language', config('app.locale', 'en')),
            'region_code' => $this->input('region_code', config('app.timezone', 'UTC')),
        ]);

        // Remove non-fillable fields from request
        $this->request->remove(['terms_accepted', 'privacy_accepted', 'password_confirmation']);
    }

    /**
     * Get the validated data from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Ensure password is properly hashed if it's in the validated data
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        return $validated;
    }
} 