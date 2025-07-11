# TASKS: Laravel Refactor & QA

## Controller Refactor
- Refactor all API and web controllers to use Form Request classes (frontend/backend subfolders)
- Ensure all validation and error messages are multi-language (JSON)

## Multi-language System
- Implement multi-language system using JSON files for all strings
- Refactor all Blade files to use translation functions

## Blade & UI Refactor
- Remove Bootstrap and all CDN references from Blade files
- Rewrite all Blade files to use TailwindCSS
- Ensure all CSS/JS is in resources and built via npm (no inline or CDN)
- Ensure only one layout is used; remove extras
- Maximize use of Blade components, but minimize number of UI component files
- Refactor to use components where possible

## Testing
- Create/complete tests for all controllers and functions (frontend/backend subfolders)
- Run all tests and fix any errors

## Data & Auth Cleanup
- Ensure all data is generated via factories/seeders
- Remove any user/auth-related code, files, and relations
