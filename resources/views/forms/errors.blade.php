@extends('layouts.forms')

@section('title', 'Error Handling Example')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Error Handling & Summary Example</h2>
    
    <p class="mb-4 text-gray-600">This example demonstrates how Aire handles validation errors and displays a summary of errors.</p>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 p-4 rounded-md">
            <h3 class="text-red-700 font-medium mb-2">Validation Error Summary</h3>
            
            {{ Aire::summary() }}
            
            <div class="mt-4 border-t border-red-200 pt-4">
                <h4 class="text-red-700 font-medium mb-2">Verbose Summary (with list)</h4>
                {{ Aire::summary()->verbose() }}
            </div>
        </div>
    @endif

    {{ Aire::open()->route('contact.submit')->id('errors-form') }}
        <div class="space-y-4">
            <div>
                {{ Aire::input('name', 'Full Name')
                    ->required()
                    ->placeholder('Enter your full name')
                    ->helpText('This field will show an error if empty')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::email('email', 'Email Address')
                    ->required()
                    ->placeholder('your.email@example.com')
                    ->helpText('Must be a valid email address')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::input('phone', 'Phone Number')
                    ->required()
                    ->placeholder('(123) 456-7890')
                    ->helpText('Format: (123) 456-7890')
                    ->class('block w-full')
                }}
            </div>

            <div>
                {{ Aire::textarea('message', 'Message')
                    ->required()
                    ->placeholder('Enter your message here...')
                    ->rows(4)
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
    
    <div class="mt-8 border-t pt-6">
        <h3 class="font-semibold text-lg mb-4">Server-Side Validation</h3>
        <p class="text-gray-700 mb-3">
            Aire will automatically pick up any errors from server-side validation and display them in the form.
            Try submitting this form without filling in the required fields to see the error handling in action.
        </p>
        
        <div class="bg-gray-50 p-4 rounded-md">
            <h4 class="font-medium text-gray-800 mb-2">How It Works:</h4>
            <ol class="list-decimal pl-5 space-y-2 text-gray-700 text-sm">
                <li>Laravel performs validation on the server-side using validation rules</li>
                <li>If validation fails, Laravel redirects back with error messages</li>
                <li>Aire automatically detects these errors and displays them next to the relevant fields</li>
                <li>The summary component provides an overview of all errors at once</li>
            </ol>
        </div>
        
        <div class="mt-4 bg-yellow-50 p-4 rounded-md">
            <h4 class="font-medium text-yellow-800">Usage Examples:</h4>
            <pre class="mt-2 text-sm text-yellow-700 overflow-x-auto">
// Basic error summary
{{ "{{ Aire::summary() }}" }}

// Verbose summary with list of errors
{{ "{{ Aire::summary()->verbose() }}" }}
            </pre>
        </div>
    </div>
</div>
@endsection 