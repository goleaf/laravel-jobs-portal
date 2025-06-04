<?php

namespace App\Http\Requests\Enhanced;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow registration for guests, or admin creating users
        return !auth()->check() || auth()->user()?->hasRole(['Admin', 'Super Admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\-\'\.]+$/'
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s\-\'\.]+$/'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
                'max:255'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'phone' => [
                'nullable',
                'string',
                'min:10',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/'
            ],
            'dob' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01'
            ],
            'gender' => [
                'nullable',
                'integer',
                'in:' . implode(',', [User::GENDER_MALE, User::GENDER_FEMALE, User::GENDER_OTHER])
            ],
            'country_id' => [
                'nullable',
                'exists:countries,id'
            ],
            'state_id' => [
                'nullable',
                'exists:states,id',
                'required_with:country_id'
            ],
            'city_id' => [
                'nullable',
                'exists:cities,id',
                'required_with:state_id'
            ],
            'user_type' => [
                'required',
                'integer',
                'in:' . implode(',', [User::TYPE_ADMIN, User::TYPE_EMPLOYER, User::TYPE_CANDIDATE])
            ],
            'facebook_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?facebook\.com\/.*$/'
            ],
            'twitter_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?twitter\.com\/.*$/'
            ],
            'linkedin_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?linkedin\.com\/.*$/'
            ],
            'google_plus_url' => [
                'nullable',
                'url',
                'max:255'
            ],
            'pinterest_url' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/(www\.)?pinterest\.com\/.*$/'
            ],
            'language' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys(User::LANGUAGES))
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot exceed 50 characters.',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 50 characters.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.regex' => 'Please provide a valid phone number format.',
            
            'dob.date' => 'Please provide a valid date of birth.',
            'dob.before' => 'Date of birth must be before today.',
            'dob.after' => 'Date of birth must be after 1900.',
            
            'gender.integer' => 'Please select a valid gender option.',
            'gender.in' => 'Please select a valid gender option.',
            
            'country_id.exists' => 'Please select a valid country.',
            'state_id.exists' => 'Please select a valid state.',
            'state_id.required_with' => 'State is required when country is selected.',
            'city_id.exists' => 'Please select a valid city.',
            'city_id.required_with' => 'City is required when state is selected.',
            
            'user_type.required' => 'User type is required.',
            'user_type.integer' => 'Please select a valid user type.',
            'user_type.in' => 'Please select a valid user type.',
            
            'facebook_url.url' => 'Please provide a valid Facebook URL.',
            'facebook_url.regex' => 'Please provide a valid Facebook profile URL.',
            'twitter_url.url' => 'Please provide a valid Twitter URL.',
            'twitter_url.regex' => 'Please provide a valid Twitter profile URL.',
            'linkedin_url.url' => 'Please provide a valid LinkedIn URL.',
            'linkedin_url.regex' => 'Please provide a valid LinkedIn profile URL.',
            'pinterest_url.url' => 'Please provide a valid Pinterest URL.',
            'pinterest_url.regex' => 'Please provide a valid Pinterest profile URL.',
            
            'language.in' => 'Please select a valid language.',
            
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file.',
            'avatar.max' => 'Avatar file size cannot exceed 2MB.',
            'avatar.dimensions' => 'Avatar dimensions must be between 100x100 and 2000x2000 pixels.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'dob' => 'date of birth',
            'user_type' => 'user type',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
            'facebook_url' => 'Facebook URL',
            'twitter_url' => 'Twitter URL',
            'linkedin_url' => 'LinkedIn URL',
            'google_plus_url' => 'Google+ URL',
            'pinterest_url' => 'Pinterest URL',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
            'first_name' => ucfirst(strtolower(trim($this->first_name))),
            'last_name' => ucfirst(strtolower(trim($this->last_name))),
        ]);

        // Clean up social media URLs
        if ($this->facebook_url && !str_starts_with($this->facebook_url, 'http')) {
            $this->merge(['facebook_url' => 'https://' . $this->facebook_url]);
        }

        if ($this->twitter_url && !str_starts_with($this->twitter_url, 'http')) {
            $this->merge(['twitter_url' => 'https://' . $this->twitter_url]);
        }

        if ($this->linkedin_url && !str_starts_with($this->linkedin_url, 'http')) {
            $this->merge(['linkedin_url' => 'https://' . $this->linkedin_url]);
        }

        if ($this->pinterest_url && !str_starts_with($this->pinterest_url, 'http')) {
            $this->merge(['pinterest_url' => 'https://' . $this->pinterest_url]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation logic
            if ($this->state_id && $this->country_id) {
                $state = \App\Models\State::find($this->state_id);
                if ($state && $state->country_id != $this->country_id) {
                    $validator->errors()->add('state_id', 'The selected state does not belong to the selected country.');
                }
            }

            if ($this->city_id && $this->state_id) {
                $city = \App\Models\City::find($this->city_id);
                if ($city && $city->state_id != $this->state_id) {
                    $validator->errors()->add('city_id', 'The selected city does not belong to the selected state.');
                }
            }

            // Validate age for certain user types
            if ($this->dob && $this->user_type) {
                $age = \Carbon\Carbon::parse($this->dob)->age;
                if ($age < 16) {
                    $validator->errors()->add('dob', 'You must be at least 16 years old to register.');
                }
            }
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'error_count' => $validator->errors()->count()
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }
} 