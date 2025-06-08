# Comprehensive Translation System Guide

## Overview

Your job portal application now has a fully comprehensive internationalization (i18n) system supporting 9 languages with advanced management tools, automatic language detection, and comprehensive translation utilities.

## Supported Languages

The system supports the following languages:

| Code | Language | Native Name | Direction | Flag |
|------|----------|-------------|-----------|------|
| `en` | English | English | LTR | 🇺🇸 |
| `ar` | Arabic | العربية | RTL | 🇸🇦 |
| `de` | German | Deutsch | LTR | 🇩🇪 |
| `es` | Spanish | Español | LTR | 🇪🇸 |
| `fr` | French | Français | LTR | 🇫🇷 |
| `pt` | Portuguese | Português | LTR | 🇵🇹 |
| `ru` | Russian | Русский | LTR | 🇷🇺 |
| `tr` | Turkish | Türkçe | LTR | 🇹🇷 |
| `zh` | Chinese | 中文 | LTR | 🇨🇳 |

## Features

### 🌍 Multi-Language Support
- **9 languages** with complete translation files
- **RTL support** for Arabic with automatic text direction detection
- **Fallback mechanism** to English for missing translations
- **Browser language detection** with automatic locale switching

### 🔧 Translation Management
- **Web-based translation manager** for administrators
- **CLI translation tools** for developers
- **Import/Export functionality** for translation files
- **Translation statistics** and coverage reports
- **Missing translation detection** and synchronization

### 🚀 Performance Optimized
- **Redis caching** for translation files
- **Lazy loading** of translation resources
- **Optimized middleware** for locale switching
- **Preloading support** for critical translations

### 🎯 Developer Experience
- **Multiple translation methods** (`__()`, `trans()`, `@lang`, `trans_json()`)
- **Artisan commands** for translation management
- **Automated scanning** for hardcoded strings
- **Translation key generation** and suggestions

## Usage Guide

### Frontend Language Switching

#### Using the Language Switcher Component

```php
{{-- Dropdown style language switcher --}}
<x-ui.language-switcher 
    type="dropdown" 
    position="bottom-right"
    :showFlags="true"
    :showNative="true"
    size="medium"
    variant="default"
/>

{{-- Compact flag-only switcher --}}
<x-ui.language-switcher 
    type="flags"
    size="small"
/>

{{-- Select dropdown --}}
<x-ui.language-switcher 
    type="select"
    :showFlags="true"
/>
```

#### Manual Language Switching

```javascript
// Switch language via AJAX
fetch('/locale/switch', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({ locale: 'de' })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        window.location.reload();
    }
});
```

### Backend Translation Usage

#### In Blade Templates

```php
{{-- Basic translation --}}
{{ __('messages.common.save') }}

{{-- Translation with parameters --}}
{{ __('messages.user.welcome', ['name' => $user->name]) }}

{{-- Blade directive --}}
@lang('messages.common.delete')

{{-- JSON translations --}}
{{ trans_json('app.title') }}
```

#### In Controllers

```php
use App\Services\TranslationService;

class MyController extends Controller
{
    public function index()
    {
        // Get translation
        $message = TranslationService::get('messages.success.created');
        
        // Get all translations for current locale
        $translations = TranslationService::getAllTranslations(app()->getLocale());
        
        // Check if translation exists
        if (TranslationService::has('messages.custom.key')) {
            // Translation exists
        }
        
        return response()->json([
            'message' => $message,
            'translations' => $translations
        ]);
    }
}
```

#### In JavaScript/Vue

```javascript
// Get current locale info
fetch('/locale/current')
    .then(response => response.json())
    .then(data => {
        console.log('Current locale:', data.current);
        console.log('Is RTL:', data.is_rtl);
        console.log('Direction:', data.direction);
    });

// Get translations for frontend
fetch('/locale/translations/en?namespace=messages')
    .then(response => response.json())
    .then(data => {
        const translations = data.translations;
        // Use translations in your frontend
    });
```

### Administration & Management

#### Web Interface

Access the translation manager at `/admin/translations`:

1. **View Translations**: Browse all translation keys and values
2. **Edit Translations**: Update existing translations
3. **Add Languages**: Create new language files
4. **Statistics**: View translation coverage and completion status
5. **Export/Import**: Backup and restore translation files

#### API Endpoints

```bash
# Get translation statistics
GET /admin/translations/statistics

# Get missing translations for a locale
GET /admin/translations/missing/de

# Sync translations from English to German
POST /admin/translations/sync/de

# Export German translations
POST /admin/translations/export/de

# Import translations to German
POST /admin/translations/import/de
```

### Command Line Tools

#### Translation Management Commands

```bash
# Show translation statistics
php artisan translation:manage stats

# Check missing translations for German
php artisan translation:manage missing --locale=de

# Sync translations from English to German
php artisan translation:manage sync --locale=de --source=en

# Scan for hardcoded strings
php artisan translation:manage scan

# Export German translations
php artisan translation:manage export --locale=de --format=json --file=storage/app/de-backup.json

# Import translations
php artisan translation:manage import --locale=de --file=storage/app/de-backup.json --merge
```

#### Cache Management

```bash
# Clear translation cache
php artisan translation:manage cache:clear

# Or via web endpoint
POST /locale/clear-cache
```

## File Structure

### Translation Files

```
lang/
├── en/
│   ├── messages.php          # Main messages
│   ├── validation.php        # Validation messages
│   ├── auth.php             # Authentication messages
│   ├── locale.php           # Locale-specific messages
│   └── ...
├── de/
│   ├── messages.php
│   ├── locale.php
│   └── ...
├── en.json                   # JSON format for frontend
├── de.json
└── ...
```

### Translation Key Structure

```php
// Common patterns
'common.save'              // Common UI elements
'common.edit'
'common.delete'

'messages.success.created' // Flash messages
'messages.error.not_found'

'validation.required'      // Validation messages
'validation.email'

'auth.failed'             // Authentication
'auth.password'

'locale.choose_language'   // Locale-specific
'locale.language_switched_successfully'

'job.title'               // Domain-specific
'job.description'
'company.name'
```

## Best Practices

### 1. Translation Key Naming

```php
// ✅ Good - Clear hierarchy and naming
'job.form.title'
'job.form.description'
'job.form.requirements'

// ❌ Bad - Unclear or flat structure
'jobFormTitle'
'job_description_field'
```

### 2. Using Parameters

```php
// ✅ Good - Clear parameter names
__('messages.user.welcome', ['name' => $user->name, 'role' => $user->role])

// Translation: "Welcome, :name! You are logged in as :role."
```

### 3. Pluralization

```php
// Use Laravel's pluralization
trans_choice('messages.items.count', $count, ['count' => $count])

// Translation file:
'items' => [
    'count' => '{0} No items|{1} One item|[2,*] :count items'
]
```

### 4. Frontend Integration

```javascript
// Create a translation helper
window.trans = (key, replace = {}) => {
    let translation = window.translations[key] || key;
    
    Object.keys(replace).forEach(placeholder => {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    });
    
    return translation;
};

// Usage
console.log(trans('messages.success.saved'));
console.log(trans('messages.user.welcome', { name: 'John' }));
```

## RTL (Right-to-Left) Support

The system automatically handles RTL languages:

### CSS Classes

```scss
// Automatic direction classes are added to <html>
html[dir="rtl"] {
    .container {
        text-align: right;
    }
    
    .btn {
        margin-left: 0;
        margin-right: 1rem;
    }
}
```

### In Blade Templates

```php
<html dir="{{ lang_direction() }}" class="{{ is_rtl() ? 'rtl' : 'ltr' }}">
    <body>
        {{-- Content automatically adapts --}}
    </body>
</html>
```

### In JavaScript

```javascript
// Check if current language is RTL
if (document.documentElement.dir === 'rtl') {
    // Apply RTL-specific logic
}
```

## Performance Optimization

### 1. Cache Preloading

```php
// In a service provider or command
TranslationService::preloadTranslations(['en', 'de', 'ar']);
```

### 2. Selective Loading

```javascript
// Load only specific namespaces
fetch('/locale/translations/en?namespace=messages')
    .then(response => response.json())
    .then(data => {
        // Only message translations loaded
    });
```

### 3. Cache Management

```php
// Clear cache when translations change
TranslationService::clearCache();

// Or clear specific locale
Cache::forget("translations_de");
```

## Troubleshooting

### Common Issues

1. **Translation not showing**
   - Check if key exists: `TranslationService::has('your.key')`
   - Verify locale is supported
   - Clear translation cache

2. **Language not switching**
   - Check middleware is applied
   - Verify locale in available_locales config
   - Check session configuration

3. **Missing translations**
   - Run: `php artisan translation:manage missing --locale=de`
   - Use sync command: `php artisan translation:manage sync --locale=de`

### Debug Mode

```php
// Enable translation debugging
config(['app.debug_translations' => true]);

// This will show missing translation keys in the UI
```

## API Reference

### LocaleController

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/locale/switch` | Switch application locale |
| GET | `/locale/current` | Get current locale info |
| GET | `/locale/available` | Get available locales |
| GET | `/locale/translations/{locale}` | Get translations |
| POST | `/locale/clear-cache` | Clear translation cache |

### TranslationService

| Method | Description |
|--------|-------------|
| `get($key, $replace, $locale)` | Get translation with fallback |
| `getAllTranslations($locale)` | Get all translations for locale |
| `has($key, $locale)` | Check if translation exists |
| `getMissingKeys($locale)` | Get missing translation keys |
| `getStatistics()` | Get translation statistics |
| `clearCache()` | Clear translation cache |

### Helper Functions

| Function | Description |
|----------|-------------|
| `trans_json($key, $replace, $locale)` | Get JSON translation |
| `is_rtl($locale)` | Check if locale is RTL |
| `lang_direction($locale)` | Get language direction |

## Conclusion

Your job portal now has a comprehensive, production-ready translation system that supports:

- ✅ 9 languages with full translation coverage
- ✅ RTL support for Arabic
- ✅ Advanced management tools
- ✅ Performance optimizations
- ✅ Developer-friendly CLI tools
- ✅ Automatic language detection
- ✅ Import/export functionality
- ✅ Translation statistics and monitoring

The system is designed to be maintainable, scalable, and easy to use for both developers and administrators.