const isProduction = process.env.NODE_ENV === 'production'

export default {
    plugins: {
        'postcss-import': {},
        tailwindcss: {},
        'postcss-preset-env': {
            stage: 1,
            features: {
                'custom-properties': false,
                'nesting-rules': true,
                'media-query-ranges': true,
                'focus-visible-pseudo-class': true
            },
            autoprefixer: {
                grid: 'autoplace',
                flexbox: 'no-2009'
            }
        },
        ...(isProduction && {
            '@fullhuman/postcss-purgecss': {
                content: [
                    './resources/**/*.blade.php',
                    './resources/**/*.js',
                    './resources/**/*.ts',
                    './resources/**/*.vue'
                ],
                defaultExtractor: content => {
                    const broadMatches = content.match(/[^<>"'`\s]*[^<>"'`\s:]/g) || []
                    const innerMatches = content.match(/[^<>"'`\s.()]*[^<>"'`\s.():]/g) || []
                    return broadMatches.concat(innerMatches)
                },
                safelist: {
                    standard: [
                        /^(bg|text|border)-(primary|secondary|success|warning|error)/,
                        /^(hover|focus|active|disabled):/,
                        /^(sm|md|lg|xl|2xl):/,
                        /^animate-/,
                        /^transition/,
                        'sr-only',
                        'group',
                        'group-hover'
                    ],
                    deep: [
                        /sweetalert2/,
                        /swal2/,
                        /vue-/,
                        /enter/,
                        /leave/
                    ]
                },
                variables: true,
                keyframes: true,
                fontFace: true
            },
            cssnano: {
                preset: ['advanced', {
                    discardComments: { removeAll: true },
                    reduceIdents: { keyframes: false },
                    discardUnused: { keyframes: false },
                    colormin: true,
                    discardDuplicates: true,
                    mergeRules: true,
                    minifySelectors: true,
                    normalizeWhitespace: true,
                    uniqueSelectors: true,
                    zindex: false
                }]
            }
        })
    }
}
