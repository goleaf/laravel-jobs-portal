@extends('layouts.forms')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Contact Us</h2>

    {{ Aire::open()->route('contact.submit')->id('contact-form') }}
        <div class="space-y-4">
            <div>
                {{ Aire::input('name', 'Full Name')
                    ->required()
                    ->placeholder('Enter your full name')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::email('email', 'Email Address')
                    ->required()
                    ->placeholder('your.email@example.com')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::select('subject', 'Subject')
                    ->options([
                        'general' => 'General Inquiry',
                        'support' => 'Technical Support',
                        'billing' => 'Billing Question',
                        'other' => 'Other'
                    ])
                    ->defaultValue('general')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::textarea('message', 'Message')
                    ->required()
                    ->placeholder('Enter your message here...')
                    ->rows(5)
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::checkbox('newsletter', 'Subscribe to our newsletter')
                    ->value(1)
                }}
            </div>

            <div class="pt-2">
                {{ Aire::submit('Send Message')
                    ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
                }}
            </div>
        </div>
    {{ Aire::close() }}
</div>
@endsection 