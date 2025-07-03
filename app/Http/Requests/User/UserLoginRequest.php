<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public login
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:180'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'g-recaptcha-response' => $this->needsCaptcha() ? ['required'] : ['nullable'],
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
            'g-recaptcha-response' => 'captcha verification',
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
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
            'g-recaptcha-response.required' => 'Please complete the captcha verification.',
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
            // Check rate limiting
            if ($this->hasTooManyLoginAttempts()) {
                $this->fireLockoutEvent();
                $seconds = RateLimiter::availableIn($this->throttleKey());

                $validator->errors()->add('email', trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]));

                return;
            }

            // Verify captcha if required
            if ($this->needsCaptcha() && $this->filled('g-recaptcha-response')) {
                if (! $this->verifyCaptcha()) {
                    $validator->errors()->add('g-recaptcha-response', 'Captcha verification failed.');
                }
            }

            // Attempt authentication if basic validation passes
            if (! $validator->errors()->any()) {
                if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
                    RateLimiter::hit($this->throttleKey());

                    $validator->errors()->add('email', 'The provided credentials are incorrect.');
                }
            }
        });
    }

    /**
     * Get the authenticated user after successful login.
     *
     * @return User
     */
    public function authenticate()
    {
        return Auth::user();
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Clear login attempts on successful validation
        RateLimiter::clear($this->throttleKey());

        // Set defaults
        $this->merge([
            'remember' => $this->boolean('remember', false),
        ]);
    }

    /**
     * Determine if the user has too many failed login attempts.
     */
    protected function hasTooManyLoginAttempts(): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            5 // Maximum 5 attempts
        );
    }

    /**
     * Fire an event when a lockout occurs.
     */
    protected function fireLockoutEvent(): void
    {
        event(new Lockout($this));
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }

    /**
     * Determine if captcha verification is needed.
     */
    protected function needsCaptcha(): bool
    {
        // Require captcha after 3 failed attempts
        return RateLimiter::attempts($this->throttleKey()) >= 3;
    }

    /**
     * Verify Google reCAPTCHA response.
     */
    protected function verifyCaptcha(): bool
    {
        $recaptchaSecret = config('services.recaptcha.secret_key');

        if (! $recaptchaSecret) {
            return true; // Skip if not configured
        }

        $response = $this->input('g-recaptcha-response');
        $remoteIp = $this->ip();

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $recaptchaSecret,
            'response' => $response,
            'remoteip' => $remoteIp,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $resultJson = json_decode($result, true);

        return isset($resultJson['success']) && $resultJson['success'] === true;
    }

    /**
     * Handle failed validation.
     *
     * @throws ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log failed login attempt
        \Log::warning('Failed login attempt', [
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'errors' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}
