# SVG Icon Components

This document catalogs all available SVG icon components for use throughout the application.

## Basic Usage

```blade
<x-icons.user class="w-5 h-5 text-gray-500" />
```

All icon components accept any HTML attributes, which will be merged with default attributes.
The default size for all icons is 24x24 pixels (w-6 h-6), but you can override this by passing a different size class.

## Available Icons

### People & Entities

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| User | `x-icons.user` | `<x-icons.user />` |
| Company | `x-icons.company` | `<x-icons.company />` |

### Objects

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| Job/Briefcase | `x-icons.job` | `<x-icons.job />` |
| Document/Resume | `x-icons.document` | `<x-icons.document />` |

### Actions

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| Search | `x-icons.search` | `<x-icons.search />` |
| Edit/Pencil | `x-icons.edit` | `<x-icons.edit />` |
| Delete/Trash | `x-icons.trash` | `<x-icons.trash />` |
| View/Eye | `x-icons.eye` | `<x-icons.eye />` |
| Close/X | `x-icons.x` | `<x-icons.x />` |

### Communication

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| Email | `x-icons.email` | `<x-icons.email />` |
| Phone | `x-icons.phone` | `<x-icons.phone />` |

### Location

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| Location | `x-icons.location` | `<x-icons.location />` |

### Time & Date

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| Calendar | `x-icons.calendar` | `<x-icons.calendar />` |
| Clock | `x-icons.clock` | `<x-icons.clock />` |

### Finance

| Icon Name | Component | Usage Example |
|-----------|-----------|---------------|
| Money/Currency | `x-icons.money` | `<x-icons.money />` |

## Customization

All icon components accept standard HTML attributes and class modifiers. You can customize the appearance of icons by passing Tailwind CSS classes:

```blade
<x-icons.user class="w-8 h-8 text-blue-500" />
```

## Sizing

The default size for all icons is 24x24 pixels (w-6 h-6), but you can override this using Tailwind's sizing utilities:

| Size | Classes | Description |
|------|---------|-------------|
| XS | `w-4 h-4` | Extra small (16px) |
| SM | `w-5 h-5` | Small (20px) |
| MD | `w-6 h-6` | Medium (24px) - Default |
| LG | `w-8 h-8` | Large (32px) |
| XL | `w-10 h-10` | Extra large (40px) |

Example:
```blade
<x-icons.email class="w-10 h-10" /> <!-- Extra large email icon -->
```

## Colors

Icons inherit text color by default. Use Tailwind's text color utilities to change the color:

```blade
<x-icons.user class="text-blue-500" /> <!-- Blue icon -->
<x-icons.search class="text-red-500" /> <!-- Red icon -->
<x-icons.location class="text-green-500" /> <!-- Green icon -->
```

## Future Icons to Create

- Settings/Gear
- Notification/Bell
- Dashboard/Home
- List/Menu
- Arrow (up, down, left, right)
- Check/Success
- Social media icons (Facebook, Twitter, LinkedIn, etc.) 