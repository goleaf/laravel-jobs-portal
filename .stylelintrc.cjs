// Stylelint config for TailwindCSS + SCSS in this project
module.exports = {
  extends: [
    'stylelint-config-standard-scss',
    'stylelint-config-recommended',
    'stylelint-config-standard',
    'stylelint-config-tailwindcss',
  ],
  plugins: ['stylelint-scss'],
  ignoreFiles: [
    'public/**/*',
    'resources/assets/**/*',
    'resources/theme/**/*',
    'resources/assets/front_web_css/**/*',
    'resources/css/vendor.css',
  ],
  rules: {
    'at-rule-no-deprecated': null,
    'at-rule-no-unknown': null,
    'scss/at-rule-no-unknown': [true, {
      ignoreAtRules: ['tailwind', 'apply', 'layer', 'variants', 'responsive', 'screen'],
    }],
    'no-invalid-double-slash-comments': null,
    'selector-class-pattern': [
      // Allow camelCase and underscores (DataTables, legacy classes)
      '^[a-z0-9_-]+$|^[a-z0-9]+([A-Z][a-z0-9]+)+$'
    ],
    'keyframes-name-pattern': null,
    'declaration-block-single-line-max-declarations': null,
    'media-feature-name-value-no-unknown': null,
    'font-family-no-missing-generic-family-keyword': null,
    'declaration-property-value-no-unknown': null,
  },
};


