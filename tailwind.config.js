/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: {
    files: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.ts',
      './resources/**/*.vue',
      './resources/**/*.jsx',
      './resources/**/*.tsx',
      './storage/framework/views/*.php',
    ],
    // Transform content to extract dynamic classes
    transform: {
      vue: (content) => {
        // Extract classes from Vue template and script sections
        return content.replace(/<!--[\s\S]*?-->/g, '')
      },
      js: (content) => {
        // Extract classes from JavaScript/TypeScript files
        return content
      }
    },
    // Enhanced extraction for complex class patterns
    extract: {
      // Custom extractor for Vue files with better class detection
      vue: (content) => {
        const classes = []
        
        // Extract class bindings: :class="{ 'class-name': condition }"
        const classBindings = content.match(/:class\s*=\s*["'][^"']*["']/g) || []
        classBindings.forEach(binding => {
          const matches = binding.match(/[\w-]+/g) || []
          classes.push(...matches)
        })
        
        // Extract template classes: class="class-name"
        const templateClasses = content.match(/class\s*=\s*["'][^"']*["']/g) || []
        templateClasses.forEach(classAttr => {
          const matches = classAttr.match(/[\w-]+/g) || []
          classes.push(...matches.slice(1)) // Skip 'class' keyword
        })
        
        // Extract dynamic class references in script
        const dynamicClasses = content.match(/['"`][\w-\s]+['"`]/g) || []
        dynamicClasses.forEach(className => {
          const clean = className.replace(/['"`]/g, '')
          if (clean.includes('-') || clean.match(/^(bg|text|border|hover|focus)/)) {
            classes.push(...clean.split(/\s+/))
          }
        })
        
        return classes
      }
    }
  },
  
  // Enhanced safelist for dynamic classes that might not be detected
  safelist: [
    // Animation classes
    {
      pattern: /^animate-/,
      variants: ['hover', 'focus', 'group-hover']
    },
    // Transition classes
    {
      pattern: /^transition/,
      variants: ['hover', 'focus']
    },
    // Color variants for our custom palette
    {
      pattern: /^(bg|text|border)-(primary|secondary|success|warning|error)-(50|100|200|300|400|500|600|700|800|900|950)$/,
      variants: ['hover', 'focus', 'active', 'disabled']
    },
    // Responsive grid classes
    {
      pattern: /^(grid-cols|col-span|row-span)-(1|2|3|4|5|6|7|8|9|10|11|12)$/,
      variants: ['sm', 'md', 'lg', 'xl', '2xl']
    },
    // Common utility classes
    'sr-only',
    'not-sr-only',
    'group',
    'group-hover',
    'peer',
    'peer-checked',
    'peer-focus',
    'peer-invalid',
    'peer-disabled',
    // Vue transition classes
    'enter-active-class',
    'leave-active-class',
    'enter-from-class',
    'enter-to-class',
    'leave-from-class',
    'leave-to-class'
  ],

  theme: {
    extend: {
      colors: {
        // Primary Brand Colors
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
          950: '#172554',
        },
        // Secondary Colors
        secondary: {
          50: '#f8fafc',
          100: '#f1f5f9',
          200: '#e2e8f0',
          300: '#cbd5e1',
          400: '#94a3b8',
          500: '#64748b',
          600: '#475569',
          700: '#334155',
          800: '#1e293b',
          900: '#0f172a',
          950: '#020617',
        },
        // Success Colors
        success: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
          950: '#052e16',
        },
        // Warning Colors
        warning: {
          50: '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#f59e0b',
          600: '#d97706',
          700: '#b45309',
          800: '#92400e',
          900: '#78350f',
          950: '#451a03',
        },
        // Error Colors
        error: {
          50: '#fef2f2',
          100: '#fee2e2',
          200: '#fecaca',
          300: '#fca5a5',
          400: '#f87171',
          500: '#ef4444',
          600: '#dc2626',
          700: '#b91c1c',
          800: '#991b1b',
          900: '#7f1d1d',
          950: '#450a0a',
        },
        // Neutral Colors for backgrounds and content
        neutral: {
          50: '#fafafa',
          100: '#f5f5f5',
          200: '#e5e5e5',
          300: '#d4d4d4',
          400: '#a3a3a3',
          500: '#737373',
          600: '#525252',
          700: '#404040',
          800: '#262626',
          900: '#171717',
          950: '#0a0a0a',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'sans-serif'],
        mono: ['JetBrains Mono', 'Fira Code', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
        display: ['Inter', 'system-ui', 'sans-serif'],
      },
      fontSize: {
        'xs': ['0.75rem', { lineHeight: '1rem' }],
        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
        'base': ['1rem', { lineHeight: '1.5rem' }],
        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
        '5xl': ['3rem', { lineHeight: '1' }],
        '6xl': ['3.75rem', { lineHeight: '1' }],
        '7xl': ['4.5rem', { lineHeight: '1' }],
        '8xl': ['6rem', { lineHeight: '1' }],
        '9xl': ['8rem', { lineHeight: '1' }],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
        '144': '36rem',
      },
      borderRadius: {
        '4xl': '2rem',
        '5xl': '2.5rem',
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'strong': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        'glow': '0 0 20px rgba(59, 130, 246, 0.15)',
        'glow-lg': '0 0 40px rgba(59, 130, 246, 0.15)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
        'bounce-soft': 'bounceSoft 0.6s ease-in-out',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.9)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        bounceSoft: {
          '0%, 20%, 53%, 80%, 100%': { transform: 'translate3d(0,0,0)' },
          '40%, 43%': { transform: 'translate3d(0, -5px, 0)' },
          '70%': { transform: 'translate3d(0, -3px, 0)' },
          '90%': { transform: 'translate3d(0, -1px, 0)' },
        },
      },
      backdropBlur: {
        xs: '2px',
      },
      transitionTimingFunction: {
        'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
        'ease-in-expo': 'cubic-bezier(0.95, 0.05, 0.795, 0.035)',
        'ease-out-expo': 'cubic-bezier(0.19, 1, 0.22, 1)',
      },
    },
  },
  
  // Optimize for better performance
  corePlugins: {
    // Disable unused core plugins to reduce bundle size
    preflight: true,
    container: true,
    accessibility: true,
    // Enable only needed plugins
    aspectRatio: true,
    backdropBlur: true,
    backdropBrightness: false,
    backdropContrast: false,
    backdropGrayscale: false,
    backdropHueRotate: false,
    backdropInvert: false,
    backdropOpacity: true,
    backdropSaturate: false,
    backdropSepia: false,
    backgroundAttachment: false,
    backgroundClip: true,
    backgroundColor: true,
    backgroundImage: true,
    backgroundOpacity: true,
    backgroundPosition: true,
    backgroundRepeat: true,
    backgroundSize: true,
    blur: true,
    brightness: false,
    contrast: false,
    cursor: true,
    display: true,
    divideColor: true,
    divideOpacity: true,
    divideStyle: true,
    divideWidth: true,
    dropShadow: true,
    fill: true,
    filter: true,
    flex: true,
    flexBasis: true,
    flexDirection: true,
    flexGrow: true,
    flexShrink: true,
    flexWrap: true,
    fontFamily: true,
    fontSize: true,
    fontSmoothing: true,
    fontStyle: true,
    fontVariantNumeric: false,
    fontWeight: true,
    gap: true,
    gradientColorStops: true,
    grayscale: false,
    gridAutoColumns: true,
    gridAutoFlow: true,
    gridAutoRows: true,
    gridColumn: true,
    gridColumnEnd: true,
    gridColumnStart: true,
    gridRow: true,
    gridRowEnd: true,
    gridRowStart: true,
    gridTemplateColumns: true,
    gridTemplateRows: true,
    height: true,
    hueRotate: false,
    invert: false,
    isolation: false,
    justifyContent: true,
    justifyItems: true,
    justifySelf: true,
    letterSpacing: true,
    lineHeight: true,
    listStylePosition: true,
    listStyleType: true,
    margin: true,
    maxHeight: true,
    maxWidth: true,
    minHeight: true,
    minWidth: true,
    mixBlendMode: false,
    objectFit: true,
    objectPosition: true,
    opacity: true,
    order: true,
    outline: true,
    overflow: true,
    overscrollBehavior: false,
    padding: true,
    placeContent: true,
    placeItems: true,
    placeSelf: true,
    placeholderColor: true,
    placeholderOpacity: true,
    pointerEvents: true,
    position: true,
    resize: false,
    ringColor: true,
    ringOffsetColor: true,
    ringOffsetWidth: true,
    ringOpacity: true,
    ringWidth: true,
    rotate: true,
    saturate: false,
    scale: true,
    scrollBehavior: false,
    scrollMargin: false,
    scrollPadding: false,
    scrollSnapAlign: false,
    scrollSnapStop: false,
    scrollSnapType: false,
    sepia: false,
    skew: true,
    space: true,
    stroke: true,
    strokeWidth: true,
    tableLayout: true,
    textAlign: true,
    textColor: true,
    textDecoration: true,
    textDecorationColor: true,
    textDecorationStyle: true,
    textDecorationThickness: true,
    textIndent: false,
    textOpacity: true,
    textOverflow: true,
    textTransform: true,
    textUnderlineOffset: false,
    transform: true,
    transformOrigin: true,
    transitionDelay: true,
    transitionDuration: true,
    transitionProperty: true,
    transitionTimingFunction: true,
    translate: true,
    userSelect: true,
    verticalAlign: true,
    visibility: true,
    whitespace: true,
    width: true,
    willChange: false,
    wordBreak: true,
    zIndex: true,
  },
  
  plugins: [],
}