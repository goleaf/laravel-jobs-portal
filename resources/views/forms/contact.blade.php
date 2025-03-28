@extends('layouts.forms')

@section('title', 'Contact Us')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Contact Us</h2>
    
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    {{ Aire::open()->route('contact.submit')->id('contact-form') }}
        
        {{ Aire::summary() }}
        
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
                        '' => 'Please select a subject',
                        'general' => 'General Inquiry',
                        'support' => 'Technical Support',
                        'billing' => 'Billing Question',
                        'other' => 'Other'
                    ])
                    ->required()
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::textarea('message', 'Your Message')
                    ->required()
                    ->placeholder('Enter your message here...')
                    ->rows(5)
                    ->class('block w-full')
                }}
            </div>

            <div class="flex items-center">
                {{ Aire::checkbox('newsletter', 'Subscribe to newsletter')
                    ->inline()
                    ->class('mr-2')
                }}
            </div>

            <div class="pt-4">
                {{ Aire::submit('Send Message')
                    ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
                }}
            </div>
        </div>
    {{ Aire::close() }}
    
    <div class="mt-8 border-t pt-6 text-sm text-gray-600">
        <p>We'll respond to your inquiry within 24-48 hours. For urgent matters, please call us at (555) 123-4567.</p>
        
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-gray-700">Our Office</h4>
                <address class="not-italic mt-1">
                    123 Main Street<br>
                    Suite 101<br>
                    Anytown, ST 12345
                </address>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-700">Hours</h4>
                <p class="mt-1">
                    Monday-Friday: 9AM-5PM<br>
                    Saturday-Sunday: Closed
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 