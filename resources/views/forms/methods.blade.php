@extends('layouts.forms')

@section('title', 'Method Spoofing Example')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Method Spoofing Example</h2>
    
    <p class="mb-4 text-gray-600">This example shows how Aire handles HTTP method spoofing for PUT, PATCH and DELETE requests.</p>

    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">PUT Method Example</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            {{ Aire::open()->method('PUT')->url('/example/resource/1')->id('put-form') }}
                <div class="space-y-4">
                    <div>
                        {{ Aire::input('name', 'Resource Name')
                            ->required()
                            ->placeholder('Enter resource name')
                            ->value('Example Resource')
                            ->class('block w-full')
                        }}
                    </div>
                    
                    <div>
                        {{ Aire::textarea('description', 'Description')
                            ->placeholder('Enter description')
                            ->value('This is an example resource description.')
                            ->rows(3)
                            ->class('block w-full')
                        }}
                    </div>
                    
                    <div class="pt-2">
                        {{ Aire::submit('Update Resource')
                            ->class('px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2')
                        }}
                    </div>
                </div>
            {{ Aire::close() }}
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-md text-sm">
            <p class="text-yellow-800 font-medium">Generated HTML will include:</p>
            <pre class="mt-2 text-yellow-700 overflow-x-auto">
&lt;form action="/example/resource/1" method="POST"&gt;
    &lt;input type="hidden" name="_method" value="PUT"&gt;
    &lt;input type="hidden" name="_token" value="..."&gt;
    ...
&lt;/form&gt;</pre>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">DELETE Method Example</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            {{ Aire::open()->method('DELETE')->url('/example/resource/1')->id('delete-form') }}
                <div class="space-y-4">
                    <div>
                        <p class="text-red-600 mb-3">Are you sure you want to delete this resource? This action cannot be undone.</p>
                    </div>
                    
                    <div class="pt-2">
                        {{ Aire::submit('Delete Resource')
                            ->class('px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2')
                        }}
                    </div>
                </div>
            {{ Aire::close() }}
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-md text-sm">
            <p class="text-yellow-800 font-medium">Generated HTML will include:</p>
            <pre class="mt-2 text-yellow-700 overflow-x-auto">
&lt;form action="/example/resource/1" method="POST"&gt;
    &lt;input type="hidden" name="_method" value="DELETE"&gt;
    &lt;input type="hidden" name="_token" value="..."&gt;
    ...
&lt;/form&gt;</pre>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Method Inference from Routes</h3>
        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <p class="text-gray-600 mb-3">Aire can automatically infer the HTTP method from named routes.</p>
            <pre class="text-gray-700 overflow-x-auto text-sm">
// In routes file:
Route::put('/example/{resource}', 'ResourceController@update')
     ->name('resource.update');

// In your Blade template:
{{ "{{ Aire::open()->route('resource.update', 1) }}" }}
   // No need to specify method('PUT')
{{ "{{ Aire::close() }}" }}</pre>
        </div>
    </div>

    <div class="mt-8 border-t pt-6">
        <h3 class="font-semibold text-lg mb-4">Method Spoofing & CSRF Protection</h3>
        <p class="text-gray-700 mb-3">
            For non-GET forms, Laravel requires:
        </p>
        <ul class="list-disc pl-5 space-y-1 text-gray-700">
            <li>A CSRF token for protection against cross-site request forgery</li>
            <li>Method spoofing for HTTP verbs other than GET/POST</li>
        </ul>
        
        <p class="mt-3 text-gray-700">
            Aire handles both automatically:
        </p>
        <ul class="list-disc pl-5 space-y-1 text-gray-700">
            <li>Adds <code class="bg-gray-100 px-1 rounded">_token</code> field for CSRF protection</li>
            <li>Adds <code class="bg-gray-100 px-1 rounded">_method</code> field for PUT/PATCH/DELETE methods</li>
        </ul>
    </div>
</div>
@endsection 