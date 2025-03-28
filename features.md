## Translation Features

- **Consolidated Translation System**: All translations are stored in single PHP files per language, making management easier
- **Multiple Language Support**: English and Lithuanian languages with standardized structure
- **Translation Helpers**: Helper class with constants for all translation keys to prevent typos
- **Custom Blade Directives**: `@t()` and `@hasTranslation()` directives for easy use in templates
- **Translation Management Commands**:
  - `translations:consolidate`: Merge all translation files into single files
  - `translations:create-lithuanian`: Generate Lithuanian translations from English
  - `translations:sync`: Synchronize translations between languages
- **Missing Translation Detection**: Automatic flagging of missing translations with placeholders
- **Standardized Translation Format**: Common format across all languages for consistency 