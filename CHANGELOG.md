# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

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
- Rappasoft/laravel-livewire-tables dependency
- Unused Vue components
- Legacy translation files
- Duplicate SVG code from blade templates 