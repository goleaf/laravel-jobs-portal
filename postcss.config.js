// PostCSS configuration for Vite + TailwindCSS
// Processes @tailwind directives, enables imports, modern CSS, and minifies in production
export default {
  plugins: {
    'postcss-import': {},
    tailwindcss: {},
    'postcss-preset-env': {
      stage: 3,
      features: {
        'nesting-rules': true,
      },
      autoprefixer: {
        grid: true,
      },
    },
    ...(process.env.NODE_ENV === 'production' ? { cssnano: { preset: 'default' } } : {}),
  },
};


