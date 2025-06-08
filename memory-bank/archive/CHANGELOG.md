# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## New Features and Improvements

### DataTable System
- Custom Livewire table implementation with Tailwind CSS styling
- Removed dependency on external datatable packages
- Streamlined table component architecture with:
  - `TableComponent` base class for all tables
  - `Column` class for flexible column configuration
  - `Filter` class for advanced filtering capabilities

### Translation System
- Consolidated translation system with a single PHP file per language
- Translation helper class with constants for standardized access
- Custom Blade directives for easier translation usage
- Lithuanian language support
- Missing translation detection
- Artisan commands for translation management:
  - `translations:consolidate` - Merges translation files
  - `translations:create-lithuanian` - Generates Lithuanian translations
  - `translations:sync` - Finds missing translations

### UI Improvements
- Updated to latest Tailwind CSS
- Standardized SVG icons as reusable components
- Improved responsive design
- Enhanced pagination with Tailwind styling

### Code Quality
- Refactored Blade templates for better readability and performance
- Optimized JavaScript files
- Standardized coding patterns
- Removed unused files and dependencies

## Removed
- Rappasoft/laravel-livewire-tables dependency

### Added
- Custom Livewire datatable implementation replacing Rappasoft
- New translation system with standardized format
- SVG extraction command to create reusable icon components
- Lithuanian language support
- New data table filters (select, multiselect, daterange, numberrange)
- Tailwind CSS pagination

### Changed
- Refactored all table components to use custom implementation
- Optimized blade templates for better performance
- Updated language configuration in config/app.php
- Improved filter components with live updates
- Better search functionality in data tables

### Removed
- Unused Vue components
- Legacy translation files
- Duplicate SVG code from blade templates 