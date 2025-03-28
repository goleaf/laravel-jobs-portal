## Additions

### Translation System
- Added consolidated translation system with single PHP file per language
- Created TranslationHelper class for standardized translation access
- Implemented custom Blade directives for translations (@t, @hasTranslation)
- Added Lithuanian language support
- Created Artisan commands for translation management (consolidate, sync, create-lithuanian)
- Added automatic detection of missing translations
- Migrated all JSON translations to PHP format 