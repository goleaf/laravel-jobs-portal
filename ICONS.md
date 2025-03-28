# Icon System Documentation

## Overview

This project implements a comprehensive SVG icon system using Laravel Blade components. Icons are centralized in the `resources/views/components/icons` directory, making them easy to maintain, update, and reuse throughout the application.

## Benefits

- **Centralized Management**: All icons are defined in a single location, making updates easy
- **Reusability**: The same icon can be used throughout the application with consistent styling
- **Customization**: Icons can be easily customized with classes or attributes
- **Improved Readability**: Template code becomes more readable, using semantic component names
- **Reduced Duplication**: No need to copy/paste SVG code in multiple places

## Available Icons

A variety of icons are available for use in the application. To see the complete list, visit `/icons` in your local development environment.

Common categories include:

- **Navigation**: home, arrow-left, arrow-right, chevron-left, chevron-right, chevron-up, chevron-down
- **User Interface**: plus, close, edit, trash, search, filter, spinner, refresh
- **Common Elements**: user, calendar, clock, location, mail, phone, document, cog, bell, globe
- **Theme**: sun, moon
- **Job-Related**: experience, salary, freelance, gender, briefcase

## Usage Examples

### Basic Usage

```blade
<x-icons.home />
```

### With Custom Size and Color

```blade
<x-icons.user class="w-8 h-8 text-blue-500" />
```

### With Additional Attributes

```blade
<x-icons.bell class="text-red-500" id="notification-icon" data-count="5" />
```

### Modifying the Icon's Appearance

Icons inherit the current text color, so you can use Tailwind's text color utilities:

```blade
<x-icons.search class="text-gray-400 hover:text-gray-600" />
```

### Animating Icons

Apply Tailwind's animation classes:

```blade
<x-icons.spinner class="animate-spin" />
```

### Responsive Icons

Adjust size based on viewport:

```blade
<x-icons.home class="w-4 h-4 md:w-6 md:h-6 lg:w-8 lg:h-8" />
```

## Creating New Icons

To add a new icon to the system:

1. Create a new Blade file in `resources/views/components/icons/`
2. Use this template:

```blade
@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <!-- SVG path data goes here -->
</svg>
```

3. Add the SVG path data inside the `<svg>` tag
4. Use the new icon in your templates with `<x-icons.your-icon-name />`

## Best Practices

1. **Consistent Sizing**: The default size for icons is 20x20 (w-5 h-5). Maintain this consistency when creating new icons.
2. **Semantic Names**: Use descriptive, semantic names for icons that reflect their purpose.
3. **Color Inheritance**: Icons should inherit colors from their parent by using `currentColor`.
4. **Clean SVGs**: Remove unnecessary attributes from SVG source files.
5. **Responsive Design**: Consider how icons will appear at different screen sizes.
6. **Accessibility**: Add `aria-hidden="true"` for decorative icons or provide appropriate labels.

## Icon Viewer

The application includes an icon viewer for easy reference during development:

- Visit `/icons` in your local environment to view all available icons
- Browse the complete library with usage examples
- Copy example code snippets to use in your templates

This icon system is designed to grow with the application, providing a consistent and maintainable approach to using SVG icons throughout the project. 