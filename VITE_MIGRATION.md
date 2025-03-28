# Migration from Laravel Mix to Vite

This project has been migrated from Laravel Mix to Vite for asset bundling. This document explains the changes made and how to work with Vite.

## Changes Made

1. Replaced Laravel Mix with Vite
2. Updated `package.json` with Vite dependencies
3. Created a `vite.config.js` configuration file
4. Updated asset references in Blade templates
5. Organized JavaScript imports into modules

## How to Use Vite

### Development

To start the Vite development server:

```bash
npm run dev
```

This will start a development server with hot module replacement. Changes to your files will be reflected immediately in the browser without a full page reload.

### Production Build

To build assets for production:

```bash
npm run build
```

This will compile and minify all assets for production use.

## Asset References

In your Blade templates, you can reference Vite assets using the `@vite` directive:

```blade
@vite(['resources/js/app.js'])
```

## JavaScript Modules

All JavaScript files are now organized as ES modules. To import functionality from other files:

```js
import { someFunction } from './some-file';
```

## CSS/SCSS

CSS and SCSS files can be imported directly in JavaScript files:

```js
import '../css/app.css';
```

## Troubleshooting

- If you encounter issues with missing assets, make sure to run `npm run build` and check that the assets are being generated in the `public/build` directory.
- Check that your Blade templates are using the correct paths for assets.
- If you're adding new files, make sure they are correctly imported in the appropriate entry points.

## Additional Resources

- [Vite Documentation](https://vitejs.dev/guide/)
- [Laravel Vite Plugin Documentation](https://laravel.com/docs/10.x/vite) 