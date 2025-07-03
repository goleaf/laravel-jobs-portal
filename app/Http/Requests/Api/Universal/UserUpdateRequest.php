<?php

namespace App\Http\Requests\Api\Universal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return auth()->check() && (
            auth()->user()->id === $user->id
            || auth()->user()->hasRole('admin')
        );
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => 'sometimes|string|max:20',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'bio' => 'sometimes|string|max:1000',
            'website' => 'sometimes|url|max:255',
            'linkedin_url' => 'sometimes|url|max:255',
            'github_url' => 'sometimes|url|max:255',
            'twitter_url' => 'sometimes|url|max:255',
            'timezone' => 'sometimes|string|max:50',
            'language' => 'sometimes|string|in:en,ar,es,fr,de,pt,ru,tr,zh',
            'notification_preferences' => 'sometimes|array',
            'notification_preferences.email_notifications' => 'boolean',
            'notification_preferences.sms_notifications' => 'boolean',
            'notification_preferences.push_notifications' => 'boolean',
            'privacy_settings' => 'sometimes|array',
            'privacy_settings.profile_visibility' => 'string|in:public,private,connections',
            'privacy_settings.show_email' => 'boolean',
            'privacy_settings.show_phone' => 'boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required when provided.',
            'email.required' => 'Email is required when provided.',
            'email.unique' => 'This email is already taken by another user.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, or JPG file.',
            'avatar.max' => 'Avatar file size cannot exceed 2MB.',
            'bio.max' => 'Bio cannot exceed 1000 characters.',
            'website.url' => 'Website must be a valid URL.',
            'linkedin_url.url' => 'LinkedIn URL must be a valid URL.',
            'language.in' => 'Language must be one of the supported languages.',
            'privacy_settings.profile_visibility.in' => 'Profile visibility must be public, private, or connections.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validate social media URLs
            $socialFields = ['linkedin_url', 'github_url', 'twitter_url'];
            foreach ($socialFields as $field) {
                if ($this->has($field) && $this->{$field}) {
                    $platform = str_replace('_url', '', $field);
                    if (! str_contains($this->{$field}, $platform.'.com')) {
                        $validator->errors()->add($field, "Please provide a valid {$platform} URL.");
                    }
                }
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'User update validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone),
            ]);
        }

        // Convert boolean strings in nested arrays
        if ($this->has('notification_preferences')) {
            $preferences = $this->notification_preferences;
            foreach (['email_notifications', 'sms_notifications', 'push_notifications'] as $field) {
                if (isset($preferences[$field])) {
                    $preferences[$field] = filter_var($preferences[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }
            }
            $this->merge(['notification_preferences' => $preferences]);
        }

        if ($this->has('privacy_settings')) {
            $settings = $this->privacy_settings;
            foreach (['show_email', 'show_phone'] as $field) {
                if (isset($settings[$field])) {
                    $settings[$field] = filter_var($settings[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }
            }
            $this->merge(['privacy_settings' => $settings]);
        }

        // Convert boolean strings
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
