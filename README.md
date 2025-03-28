# InfyJobs


### Process to setup project : 
- Clone the repo
- Set your database information into `.env`
    - Set DB env variables
    - Set Mail env variables
- Run `composer install`
- Run `npm install`
- Run `npm run dev`
- Run `php artisan migrate`
- And you are ready to go.

#### Commit Rules :
There are some standard commit rules that helps you to underhand what code are committed by reading specific commit. 
Follow the given rules while committing :  
- Wrap lines at 72 characters
- Follow the conversational commit rules e.g (`<type>[module name / scope]: <description>`)
 - You can find commit types and module names into below section.  
 - Commit should be done as : `feat(users): users crud added`
 
##### Commit Types
    - feat (use this when you want to commit new feature) 
    - refactor (use this when some code refactored)
    - style (use this when style related changes are made)
    - fix (use this when you have fixed some bugs/issues)
    - docs (use this when docs related changes are made)
    - chore (use this when composer/package or any other libraries are installed)
    
## Translation System

The application now uses a consolidated translation system that:

1. **Consolidates translations** - All translations are stored in a single PHP file per language (e.g., `lang/en.php`)
2. **Standardizes access** - Use the TranslationHelper class constants or the `@t()` Blade directive
3. **Supports multiple languages** - Currently English and Lithuanian are supported
4. **Detects missing translations** - Missing translations are automatically flagged

### Translation Commands

- `php artisan translations:consolidate` - Merge all translation files into a single file per language
- `php artisan translations:create-lithuanian` - Generate Lithuanian translations from English
- `php artisan translations:sync` - Find and add missing translations between languages

### Translation Usage in Blade Templates

Use the new custom Blade directives for translations:

```blade
{{-- Using the @t directive --}}
<h1>@t('messages.common.title')</h1>

{{-- Using the @hasTranslation directive --}}
@hasTranslation('messages.common.subtitle')
    <h2>@t('messages.common.subtitle')</h2>
@endhasTranslation
```

Or use the TranslationHelper class constants in PHP:

```php
use App\Helpers\TranslationHelper;

echo TranslationHelper::getTranslation(TranslationHelper::COMMON_TITLE);
```
