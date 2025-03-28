# Aire Forms Integration Documentation

## Overview

This project integrates [Aire](https://airephp.com/), a modern Laravel form builder with a fluent interface. Aire enables developers to create beautiful, responsive forms with minimal effort while maintaining clean, expressive code.

Our implementation offers:

- Consistent Tailwind CSS styling across all forms
- Client-side validation capabilities
- Integration with Alpine.js for dynamic forms
- Comprehensive error handling
- Data binding from multiple sources
- HTTP method spoofing for RESTful applications
- Accessibility considerations built-in

## Available Examples

To showcase the capabilities of Aire, we've created several example forms:

### 1. Contact Form
**Route**: `/contact`

A standard contact form that demonstrates basic Aire implementation with:
- Input fields (text, email)
- Select dropdown
- Textarea
- Checkbox
- Submit button
- Success message handling

### 2. Validation Example
**Route**: `/forms/validation`

Demonstrates client-side validation with:
- Required field validation
- Email format validation
- Minimum length validation
- Pattern matching (regex)
- Password confirmation matching
- Dropdown required selection

### 3. Alpine.js Integration 
**Route**: `/forms/alpine`

Shows how Aire works with Alpine.js to create dynamic forms:
- Conditional field display
- Dynamic form sections based on selection
- Checkbox toggles for showing/hiding fields
- Live counters and feedback
- Multiple choice selection with real-time updates

### 4. Binding Example
**Route**: `/forms/binding`

Demonstrates how Aire handles data binding:
- Pre-filled form fields from array data
- How to bind Eloquent models
- Binding precedence demonstration
- Working with arrays and objects

### 5. Error Handling
**Route**: `/forms/errors`

Shows comprehensive error handling capabilities:
- Server-side validation error display
- Error summary component
- Field-specific error messages
- Verbose error listings
- Error styling consistency

### 6. Method Spoofing
**Route**: `/forms/methods`

Demonstrates HTTP method spoofing for RESTful applications:
- PUT method form example
- DELETE method form example
- Method inference from routes
- Automatic CSRF token inclusion
- Hidden field generation for Laravel compatibility

## Core Components

### Form Element

The foundation of any Aire form is the opening and closing tags:

```php
{{ Aire::open()->route('contact.submit')->id('form-id') }}
    // Form elements go here
{{ Aire::close() }}
```

Common options:
- `->method('POST')` - Set HTTP method (GET, POST, PUT, etc.)
- `->route('route.name')` - Set form action using a named route
- `->url('/submit-url')` - Set form action using a direct URL
- `->id('form-id')` - Set form ID attribute
- `->class('custom-class')` - Add custom classes

### Input Fields

Basic text input:
```php
{{ Aire::input('field_name', 'Label Text')
    ->placeholder('Enter text here')
    ->required()
    ->class('custom-class') }}
```

Email input:
```php
{{ Aire::email('email', 'Email Address')
    ->required()
    ->placeholder('your.email@example.com') }}
```

Password input:
```php
{{ Aire::password('password', 'Your Password')
    ->required()
    ->placeholder('Enter a secure password') }}
```

### Select Dropdowns

```php
{{ Aire::select('subject', 'Subject')
    ->options([
        '' => 'Please select a subject',
        'general' => 'General Inquiry',
        'support' => 'Technical Support',
        'billing' => 'Billing Question'
    ])
    ->required() }}
```

### Textareas

```php
{{ Aire::textarea('message', 'Your Message')
    ->rows(5)
    ->required()
    ->placeholder('Enter your message here...') }}
```

### Checkboxes and Radio Buttons

Checkbox:
```php
{{ Aire::checkbox('newsletter', 'Subscribe to newsletter')
    ->inline()
    ->value(1) }}
```

Radio:
```php
{{ Aire::radio('gender', 'Male')
    ->inline()
    ->value('male') }}

{{ Aire::radio('gender', 'Female')
    ->inline()
    ->value('female') }}
```

### Submit Buttons

```php
{{ Aire::submit('Send Message')
    ->class('primary-button') }}
```

### File Uploads

Aire supports file upload fields with custom styling:

```php
{{ Aire::file('document', 'Upload Document')
    ->accept('.pdf,.doc,.docx')
    ->helpText('Max file size: 5MB. Accepted formats: PDF, DOC, DOCX')
    ->required() }}
```

For multiple file uploads:

```php
{{ Aire::file('photos[]', 'Upload Photos')
    ->multiple()
    ->accept('image/*')
    ->helpText('Upload up to 5 images') }}
```

## Validation Implementation

### Client-Side Validation

To add client-side validation to a form element, use the `rules()` method:

```php
{{ Aire::input('name', 'Full Name')
    ->rules('required|min:3')
    ->helpText('Your name must be at least 3 characters') }}
```

Common validation rules:
- `required` - Field must not be empty
- `email` - Must be a valid email format
- `min:n` - Minimum length of n characters
- `max:n` - Maximum length of n characters
- `same:field` - Must match another field (like password confirmation)
- `regex:/pattern/` - Must match regex pattern

### Server-Side Validation

Server-side validation is handled by Laravel's validator in the controller:

```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'subject' => 'required|string|in:general,support,billing,other',
    'message' => 'required|string',
]);
```

Aire automatically displays these validation errors when the page is reloaded.

### Error Summary

To display an error summary at the top of the form:

```php
{{ Aire::summary() }}
```

For a more detailed summary with a list of all errors:

```php
{{ Aire::summary()->verbose() }}
```

## Data Binding

### Binding Arrays

```php
{{ Aire::bind([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'message' => 'This is a pre-filled message.'
]) }}
```

### Binding Eloquent Models

```php
// In controller
$user = User::find(1);
return view('form.edit', compact('user'));

// In view
{{ Aire::bind($user) }}
```

### Binding Objects

```php
{{ Aire::bind((object) [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]) }}
```

## Method Spoofing

### Setting HTTP Methods

HTML forms only support GET and POST methods, but Laravel applications often use PUT, PATCH, and DELETE for RESTful operations. Aire automatically adds the necessary `_method` hidden field:

```php
{{ Aire::open()->method('PUT')->url('/example/resource/1') }}
    // Form fields
{{ Aire::close() }}
```

This generates:

```html
<form action="/example/resource/1" method="POST">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="...">
    <!-- Form fields -->
</form>
```

### Method Inference from Routes

Aire can automatically determine the HTTP method from a named route:

```php
// In routes file:
Route::put('/example/{resource}', 'ResourceController@update')
     ->name('resource.update');

// In your Blade template:
{{ Aire::open()->route('resource.update', 1) }}
   // No need to specify method('PUT')
{{ Aire::close() }}
```

### CSRF Protection

For non-GET forms, Laravel requires a CSRF token. Aire automatically adds the `_token` hidden field for you:

```php
{{ Aire::open()->method('POST')->url('/example/contact') }}
    // Form fields
{{ Aire::close() }}
```

This generates:

```html
<form action="/example/contact" method="POST">
    <input type="hidden" name="_token" value="...">
    <!-- Form fields -->
</form>
```

## Alpine.js Integration

To make a form dynamic with Alpine.js:

```php
<div x-data="{ showPhone: false }">
    <div class="flex items-center">
        {{ Aire::checkbox('show_phone')
            ->value('1')
            ->id('show_phone')
            ->attribute('x-model', 'showPhone') }}
        <label for="show_phone" class="ml-2">Add phone number</label>
    </div>

    <div x-show="showPhone" x-transition>
        {{ Aire::input('phone', 'Phone Number')
            ->placeholder('(123) 456-7890') }}
    </div>
</div>
```

## Custom Configuration

Our custom configuration in `config/aire.php` includes Tailwind-specific default classes:

```php
'default_classes' => [
    'group' => 'mb-4',
    'input' => 'mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md',
    'label' => 'block text-sm font-medium text-gray-700 mb-1',
    'button' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
    'help_text' => 'mt-2 text-sm text-gray-500',
    'error_text' => 'mt-2 text-sm text-red-600',
    // ...
],
```

## Accessibility

Aire forms are built with accessibility in mind, but there are several ways to enhance accessibility further:

### ARIA Attributes

Add ARIA attributes to form elements for improved screen reader support:

```php
{{ Aire::input('search', 'Search')
    ->attribute('aria-label', 'Search products')
    ->attribute('aria-describedby', 'search-description') }}

<div id="search-description" class="sr-only">
    Enter product name or keywords to search our catalog
</div>
```

### Accessible Labels

Always use labels with form controls. When visual labels aren't desired, use `aria-label` or `aria-labelledby`:

```php
// With visible label
{{ Aire::input('name', 'Your Name') }}

// With visually hidden label
<div class="sr-only">Your Name</div>
{{ Aire::input('name')
    ->attribute('aria-labelledby', 'name-label') }}
```

### Focus Management

Ensure proper focus management for dynamic forms:

```php
<div x-data="{ showExtraFields: false }">
    {{ Aire::checkbox('show_extra')
        ->label('Show additional fields')
        ->attribute('x-model', 'showExtraFields')
        ->attribute('@change', 'showExtraFields ? $nextTick(() => $refs.firstExtra.focus()) : null') }}
    
    <div x-show="showExtraFields">
        {{ Aire::input('extra_field')
            ->attribute('x-ref', 'firstExtra') }}
    </div>
</div>
```

### Color Contrast

Ensure proper color contrast in form elements, especially for error states:

```php
// In config/aire.php
'default_classes' => [
    'error_text' => 'mt-2 text-red-800', // Darker red for better contrast
]
```

## Internationalization

### Translating Form Labels and Messages

For multi-language applications, you can use Laravel's localization features with Aire:

```php
{{ Aire::input('name', __('forms.name'))
    ->placeholder(__('forms.name_placeholder'))
    ->helpText(__('forms.name_help')) }}
```

### Translating Validation Messages

Laravel's validation messages are automatically translated. For custom client-side validation messages:

```php
{{ Aire::input('name', __('forms.name'))
    ->rules('required|min:3')
    ->validationMessages([
        'required' => __('validation.custom.name.required'),
        'min' => __('validation.custom.name.min')
    ]) }}
```

### Right-to-Left (RTL) Support

For RTL languages, add the appropriate attributes to your form:

```php
{{ Aire::open()
    ->route('contact.submit')
    ->attribute('dir', 'rtl')
    ->attribute('lang', 'ar') }}
```

## Complex Form Layouts

### Multi-Column Forms

Create multi-column layouts with Tailwind's grid system:

```php
{{ Aire::open()->route('user.store') }}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            {{ Aire::input('first_name', 'First Name')->required() }}
        </div>
        <div>
            {{ Aire::input('last_name', 'Last Name')->required() }}
        </div>
    </div>
    
    <div class="mt-4">
        {{ Aire::textarea('bio', 'Biography') }}
    </div>
    
    <div class="mt-4">
        {{ Aire::submit('Save') }}
    </div>
{{ Aire::close() }}
```

### Form Sections

Organize complex forms into logical sections:

```php
{{ Aire::open()->route('checkout.process') }}
    <div class="space-y-8">
        <section>
            <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{ Aire::input('name', 'Full Name')->required() }}
                {{ Aire::email('email', 'Email Address')->required() }}
            </div>
        </section>
        
        <section>
            <h3 class="text-lg font-medium text-gray-900">Shipping Address</h3>
            <div class="mt-4 space-y-4">
                {{ Aire::input('address', 'Street Address')->required() }}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{ Aire::input('city', 'City')->required() }}
                    {{ Aire::input('state', 'State/Province')->required() }}
                    {{ Aire::input('zip', 'Postal Code')->required() }}
                </div>
            </div>
        </section>
        
        <section>
            <h3 class="text-lg font-medium text-gray-900">Payment Information</h3>
            <div class="mt-4 space-y-4">
                {{ Aire::input('card_number', 'Card Number')
                    ->required()
                    ->placeholder('0000 0000 0000 0000') }}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{ Aire::input('expiry', 'Expiry Date')
                        ->required()
                        ->placeholder('MM/YY') }}
                    {{ Aire::input('cvv', 'CVV')
                        ->required()
                        ->placeholder('123') }}
                </div>
            </div>
        </section>
        
        <div class="pt-4">
            {{ Aire::submit('Complete Purchase')
                ->class('w-full md:w-auto') }}
        </div>
    </div>
{{ Aire::close() }}
```

### Wizard-Style Forms

For multi-step forms, combine Aire with Alpine.js:

```php
<div x-data="{ step: 1, maxStep: 3 }" class="space-y-8">
    {{ Aire::open()->route('multi-step.submit') }}
        <div class="mb-6">
            <div class="flex items-center">
                <template x-for="i in maxStep" :key="i">
                    <div class="flex items-center">
                        <div :class="{'bg-blue-500': step >= i, 'bg-gray-300': step < i}" 
                             class="rounded-full h-8 w-8 flex items-center justify-center text-white">
                            <span x-text="i"></span>
                        </div>
                        <div x-show="i < maxStep" class="h-1 w-10" :class="{'bg-blue-500': step > i, 'bg-gray-300': step <= i}"></div>
                    </div>
                </template>
            </div>
        </div>
        
        <div x-show="step === 1">
            <h3 class="text-lg font-medium mb-4">Personal Information</h3>
            {{ Aire::input('name', 'Name')->required() }}
            {{ Aire::email('email', 'Email')->required() }}
            <div class="mt-4 flex justify-end">
                <button type="button" @click="step++" class="px-4 py-2 bg-blue-500 text-white rounded">
                    Next Step
                </button>
            </div>
        </div>
        
        <div x-show="step === 2" x-cloak>
            <h3 class="text-lg font-medium mb-4">Profile Details</h3>
            {{ Aire::textarea('bio', 'Biography') }}
            {{ Aire::input('occupation', 'Occupation') }}
            <div class="mt-4 flex justify-between">
                <button type="button" @click="step--" class="px-4 py-2 bg-gray-300 text-gray-800 rounded">
                    Previous Step
                </button>
                <button type="button" @click="step++" class="px-4 py-2 bg-blue-500 text-white rounded">
                    Next Step
                </button>
            </div>
        </div>
        
        <div x-show="step === 3" x-cloak>
            <h3 class="text-lg font-medium mb-4">Confirmation</h3>
            <p class="mb-4">Please review your information before submitting.</p>
            {{ Aire::checkbox('terms', 'I agree to the terms and conditions')
                ->required() }}
            <div class="mt-4 flex justify-between">
                <button type="button" @click="step--" class="px-4 py-2 bg-gray-300 text-gray-800 rounded">
                    Previous Step
                </button>
                {{ Aire::submit('Complete Registration')
                    ->class('px-4 py-2 bg-blue-500 text-white rounded') }}
            </div>
        </div>
    {{ Aire::close() }}
</div>
```

## Best Practices

1. **Keep Forms Organized**: Group related fields together using divs or fieldsets
2. **Use Helper Text**: Provide context with `->helpText('Helper message')`
3. **Consistent Styling**: Use the default classes or override consistently
4. **Validation Feedback**: Always provide clear validation rules and feedback
5. **Progressive Enhancement**: Ensure forms work without JavaScript
6. **Accessibility**: Use proper labels and ARIA attributes
7. **Field IDs**: Explicitly set IDs for custom JavaScript interaction
8. **RESTful Methods**: Use appropriate HTTP verbs (GET, POST, PUT, DELETE) to match your application's RESTful design
9. **Error Handling**: Always display validation errors clearly
10. **Internationalization**: Design forms with translation in mind

## Troubleshooting

### Common Issues

1. **Validation Not Working**: Ensure you're using the correct validation syntax and that client-side validation is enabled in config
2. **Styling Inconsistencies**: Check for conflicting CSS or incorrect Tailwind classes
3. **Data Not Binding**: Verify the data structure matches field names
4. **Alpine.js Issues**: Make sure Alpine.js is properly loaded before form initialization
5. **Method Spoofing**: If you're having issues with PUT/DELETE routes, verify your Laravel routes are properly configured
6. **File Upload Problems**: Check enctype attribute is set correctly (Aire handles this automatically)
7. **Layout Issues**: Use Tailwind's responsive utilities to ensure forms look good at all screen sizes

### Memory Usage

When running Aire with large forms, you may encounter memory limit issues when publishing assets:

```bash
PHP Fatal error: Allowed memory size of 134217728 bytes exhausted
```

Increase PHP memory limit when running these commands:

```bash
php -d memory_limit=512M artisan vendor:publish --tag=aire-views
```

## Performance Considerations

For large forms or applications with many forms, consider these performance optimizations:

1. **Defer Scripts**: Use `defer` attribute on Alpine.js and other script imports
2. **Lazy Loading**: Only load form-specific JavaScript when needed
3. **Minimize DOM Manipulations**: Avoid excessive changes to the DOM in Alpine.js components
4. **Cache Configuration**: Consider caching Aire configuration in production
5. **Optimize Validation**: Use server-side validation for complex rules
6. **Reduce DOM Size**: Break extremely large forms into steps or sections

## Additional Resources

- [Aire Official Documentation](https://airephp.com/)
- [Laravel Validation Documentation](https://laravel.com/docs/validation)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Web Content Accessibility Guidelines (WCAG)](https://www.w3.org/WAI/standards-guidelines/wcag/)
- [MDN Forms Documentation](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form) 