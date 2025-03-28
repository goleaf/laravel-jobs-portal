@extends('layouts.forms')

@section('title', 'Data Binding Example')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Model Binding Example</h2>
    
    <p class="mb-4 text-gray-600">This example demonstrates how Aire can bind data from models, arrays, or objects.</p>

    {{ Aire::open()->route('contact.submit')->id('binding-form') }}
        
        <div class="mb-6 p-4 bg-blue-50 rounded-md">
            <h3 class="font-semibold text-blue-800 mb-2">Form with pre-filled data</h3>
            <p class="text-sm text-blue-600 mb-3">The data is pre-bound to the form.</p>
            
            {{ Aire::bind([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'general',
                'message' => 'This is a pre-filled message to demonstrate binding.',
                'newsletter' => true
            ]) }}
            
            <div class="space-y-4">
                <div>
                    {{ Aire::input('name', 'Full Name')
                        ->required()
                        ->class('block w-full')
                    }}
                </div>

                <div>
                    {{ Aire::email('email', 'Email Address')
                        ->required()
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
                        ->class('block w-full')
                    }}
                </div>

                <div>
                    {{ Aire::textarea('message', 'Your Message')
                        ->required()
                        ->rows(4)
                        ->class('block w-full')
                    }}
                </div>

                <div class="flex items-center">
                    {{ Aire::checkbox('newsletter', 'Subscribe to newsletter')
                        ->inline()
                        ->class('mr-2')
                    }}
                </div>
            </div>
        </div>
        
        <div class="pt-4">
            {{ Aire::submit('Submit Form')
                ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
            }}
        </div>
    {{ Aire::close() }}
    
    <div class="mt-8 border-t pt-6">
        <h3 class="font-semibold text-lg mb-4">Binding Precedence</h3>
        <ol class="list-decimal pl-5 space-y-2 text-gray-700">
            <li>Values set with <code class="bg-gray-100 px-1 rounded">value()</code> are applied no matter what</li>
            <li>Old input is applied if available</li>
            <li>Bound data is applied last</li>
        </ol>
        
        <div class="mt-4 bg-yellow-50 p-4 rounded-md">
            <h4 class="font-medium text-yellow-800">Usage Examples:</h4>
            <pre class="mt-2 text-sm text-yellow-700 overflow-x-auto">
// Bind Eloquent models
Aire::bind(User::find(1));

// Bind an array
Aire::bind(['name' => 'Chris']);

// Bind any object
Aire::bind((object) ['name' => 'Chris']);
            </pre>
        </div>
    </div>
</div>
@endsection 