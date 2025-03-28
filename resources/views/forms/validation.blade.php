@extends('layouts.forms')

@section('title', 'Form Validation Example')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Client-Side Validation Example</h2>

    {{ Aire::open()->route('contact.submit')->id('validation-form') }}
        <div class="space-y-4">
            <div>
                {{ Aire::input('name', 'Full Name')
                    ->required()
                    ->placeholder('Enter your full name')
                    ->rules('required|min:3')
                    ->helpText('Your name must be at least 3 characters')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::email('email', 'Email Address')
                    ->required()
                    ->placeholder('your.email@example.com')
                    ->rules('required|email')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::input('phone', 'Phone Number')
                    ->required()
                    ->placeholder('(123) 456-7890')
                    ->rules('required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10')
                    ->helpText('Enter a valid phone number')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::password('password', 'Password')
                    ->required()
                    ->placeholder('Enter a secure password')
                    ->rules('required|min:8')
                    ->helpText('Password must be at least 8 characters')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::password('password_confirmation', 'Confirm Password')
                    ->required()
                    ->placeholder('Confirm your password')
                    ->rules('required|same:password')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::select('country', 'Country')
                    ->options([
                        '' => 'Select your country',
                        'us' => 'United States',
                        'ca' => 'Canada',
                        'uk' => 'United Kingdom',
                        'au' => 'Australia'
                    ])
                    ->rules('required')
                    ->class('block w-full')
                }}
            </div>

            <div class="pt-4">
                {{ Aire::submit('Submit Form')
                    ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
                }}
            </div>
        </div>
    {{ Aire::close() }}
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Any additional JavaScript for enhanced validation can go here
    });
</script>
@endpush 