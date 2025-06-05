<!-- Form Helpers README -->
<div class="container mx-auto px-4 mx-auto">
    <h1>Form Helpers Documentation</h1>
    
    <h2>Introduction</h2>
    <p>
        This form helper system provides Laravel Collective HTML-like functionality for generating forms and form elements in your Blade templates.
        It allows you to quickly create forms with consistent styling and behavior.
    </p>

    <h2>Basic Usage</h2>
    <p>Here's how to create a basic form:</p>
    <pre><code>
    @formOpen(['url' => route('posts.store'), 'method' => 'POST', 'files' => true])
        <!-- Form fields go here -->
        {{ Form::text('title', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => 'Enter title']) }}
        {{ Form::textarea('content', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'rows' => 5]) }}
        {{ Form::submit('Save', ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors']) }}
    @formClose()
    </code></pre>

    <h2>Available Methods</h2>
    <h3>Form Open & Close</h3>
    <ul>
        <li><code>@formOpen($options)</code> - Opens a form with specified options</li>
        <li><code>@formClose()</code> - Closes a form</li>
    </ul>

    <h3>Form Controls</h3>
    <ul>
        <li><code>Form::text($name, $value, $attributes)</code> - Creates a text input</li>
        <li><code>Form::password($name, $attributes)</code> - Creates a password input</li>
        <li><code>Form::email($name, $value, $attributes)</code> - Creates an email input</li>
        <li><code>Form::file($name, $attributes)</code> - Creates a file input</li>
        <li><code>Form::textarea($name, $value, $attributes)</code> - Creates a textarea</li>
    </ul>

    <h3>Selection Inputs</h3>
    <ul>
        <li><code>Form::select($name, $options, $selected, $attributes)</code> - Creates a dropdown select</li>
        <li><code>Form::checkbox($name, $value, $checked, $attributes)</code> - Creates a checkbox</li>
        <li><code>Form::radio($name, $value, $checked, $attributes)</code> - Creates a radio button</li>
    </ul>

    <h3>Buttons</h3>
    <ul>
        <li><code>Form::submit($value, $attributes)</code> - Creates a submit button</li>
        <li><code>Form::button($value, $attributes)</code> - Creates a generic button</li>
    </ul>

    <h3>Labels</h3>
    <ul>
        <li><code>Form::label($for, $text, $attributes)</code> - Creates a label</li>
    </ul>

    <h2>Examples</h2>

    <h3>Login Form Example</h3>
    <pre><code>
    @formOpen(['url' => route('login'), 'id' => 'login-form'])
        @csrf
        <div class="form-group">
            {{ Form::label('email', 'Email Address:') }}
            {{ Form::email('email', old('email'), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}
        </div>
        
        <div class="form-group">
            {{ Form::label('password', 'Password:') }}
            {{ Form::password('password', ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}
        </div>
        
        <div class="flex items-center">
            {{ Form::checkbox('remember', 1, old('remember'), ['class' => 'form-check-input', 'id' => 'remember']) }}
            {{ Form::label('remember', 'Remember Me', ['class' => 'form-check-label']) }}
        </div>
        
        {{ Form::submit('Login', ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors']) }}
    @formClose()
    </code></pre>

    <h3>Dropdown Select Example</h3>
    <pre><code>
    {{ Form::label('country', 'Country:') }}
    {{ Form::select('country', [
        'us' => 'United States',
        'ca' => 'Canada',
        'mx' => 'Mexico'
    ], null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => 'Select a country']) }}
    </code></pre>

    <h3>File Upload Example</h3>
    <pre><code>
    @formOpen(['url' => route('uploads.store'), 'method' => 'POST', 'files' => true])
        <div class="form-group">
            {{ Form::label('document', 'Upload Document:') }}
            {{ Form::file('document', ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}
        </div>
        {{ Form::submit('Upload', ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors']) }}
    @formClose()
    </code></pre>

    <h2>Integration with Bootstrap</h2>
    <p>These form helpers work well with Bootstrap styling. Just add the appropriate Bootstrap classes to your form elements:</p>
    <pre><code>
    {{ Form::text('name', null, [
        'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        'placeholder' => 'Enter your name'
    ]) }}
    </code></pre>

    <h2>Working with Validation Errors</h2>
    <p>You can easily add error handling to your forms:</p>
    <pre><code>
    <div class="form-group">
        {{ Form::label('email', 'Email:') }}
        {{ Form::email('email', old('email'), [
            'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ' . ($errors->has('email') ? 'is-invalid' : '')
        ]) }}
        @if ($errors->has('email'))
            <div class="invalid-feedback">
                {{ $errors->first('email') }}
            </div>
        @endif
    </div>
    </code></pre>
</div> 