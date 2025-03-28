@extends('layouts.forms')

@section('title', 'Alpine.js Integration')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Alpine.js Integration Example</h2>

    <div x-data="{ formType: 'contact', showPhone: false, interests: [] }">
        {{ Aire::open()->route('contact.submit')->id('alpine-form') }}
            <div class="space-y-4">
                <div>
                    {{ Aire::select('form_type', 'Form Type')
                        ->options([
                            'contact' => 'Contact Us',
                            'support' => 'Technical Support',
                            'feedback' => 'Feedback'
                        ])
                        ->defaultValue('contact')
                        ->class('block w-full')
                        ->id('form_type')
                        ->attribute('x-model', 'formType')
                    }}
                </div>

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

                <div class="flex items-center mb-4">
                    <div class="flex items-center h-5">
                        {{ Aire::checkbox('show_phone')
                            ->value('1')
                            ->id('show_phone')
                            ->attribute('x-model', 'showPhone')
                        }}
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="show_phone" class="font-medium text-gray-700">Add phone number</label>
                    </div>
                </div>

                <div x-show="showPhone" x-transition>
                    {{ Aire::input('phone', 'Phone Number')
                        ->placeholder('(123) 456-7890')
                        ->class('block w-full')
                    }}
                </div>

                <div x-show="formType === 'feedback'" x-transition>
                    <fieldset class="border border-gray-200 p-4 rounded-md">
                        <legend class="text-sm font-medium text-gray-700 px-2">What are you interested in?</legend>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                {{ Aire::checkbox('interests[]')
                                    ->value('product')
                                    ->id('interest_product')
                                    ->attribute('x-model', 'interests')
                                }}
                                <label for="interest_product" class="ml-3 text-sm text-gray-700">Products</label>
                            </div>
                            <div class="flex items-start">
                                {{ Aire::checkbox('interests[]')
                                    ->value('service')
                                    ->id('interest_service')
                                    ->attribute('x-model', 'interests')
                                }}
                                <label for="interest_service" class="ml-3 text-sm text-gray-700">Services</label>
                            </div>
                            <div class="flex items-start">
                                {{ Aire::checkbox('interests[]')
                                    ->value('support')
                                    ->id('interest_support')
                                    ->attribute('x-model', 'interests')
                                }}
                                <label for="interest_support" class="ml-3 text-sm text-gray-700">Support</label>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div x-show="formType === 'support'" x-transition>
                    {{ Aire::select('priority', 'Priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'critical' => 'Critical'
                        ])
                        ->defaultValue('medium')
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

                <div class="pt-4">
                    {{ Aire::submit('Send Message')
                        ->class('w-full px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2')
                    }}
                </div>
                
                <div x-show="interests.length > 0" class="text-sm text-gray-500">
                    You selected <span x-text="interests.length"></span> interests.
                </div>
            </div>
        {{ Aire::close() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
@endpush 