# Aire Forms Integration Documentation

## Overview

This project integrates [Aire](https://airephp.com/), a modern Laravel form builder with a fluent interface. Aire enables developers to create beautiful, responsive forms with minimal effort while maintaining clean, expressive code.

Our implementation offers:

- Consistent Tailwind CSS styling across all forms
- Client-side validation capabilities
- Integration with Alpine.js for dynamic forms
- Comprehensive error handling
- Data binding from multiple sources

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

## Best Practices

1. **Keep Forms Organized**: Group related fields together using divs or fieldsets
2. **Use Helper Text**: Provide context with `->helpText('Helper message')`
3. **Consistent Styling**: Use the default classes or override consistently
4. **Validation Feedback**: Always provide clear validation rules and feedback
5. **Progressive Enhancement**: Ensure forms work without JavaScript
6. **Accessibility**: Use proper labels and ARIA attributes
7. **Field IDs**: Explicitly set IDs for custom JavaScript interaction

## Troubleshooting

### Common Issues

1. **Validation Not Working**: Ensure you're using the correct validation syntax and that client-side validation is enabled in config
2. **Styling Inconsistencies**: Check for conflicting CSS or incorrect Tailwind classes
3. **Data Not Binding**: Verify the data structure matches field names
4. **Alpine.js Issues**: Make sure Alpine.js is properly loaded before form initialization

### Memory Usage

When running Aire with large forms, you may encounter memory limit issues when publishing assets:

```bash
PHP Fatal error: Allowed memory size of 134217728 bytes exhausted
```

Increase PHP memory limit when running these commands:

```bash
php -d memory_limit=512M artisan vendor:publish --tag=aire-views
```

## Additional Resources

- [Aire Official Documentation](https://airephp.com/)
- [Laravel Validation Documentation](https://laravel.com/docs/validation)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs) 