/** @type {import('stylelint').Config} */
export default {
  extends: ['stylelint-config-standard', 'stylelint-config-standard-scss', 'stylelint-config-recess-order'],
  overrides: [
    {
      files: ['**/*.scss'],
      rules: {
        'at-rule-no-unknown': null,
        'number-max-precision': 5,
      },
    },
    {
      files: ['**/*.{js,ts,jsx,tsx}'],
      customSyntax: 'postcss-styled-syntax',
      rules: {
        'at-rule-no-unknown': null,
      },
    },
  ],
  rules: {
    'at-rule-no-unknown': null,
    'number-max-precision': 5,
    'selector-class-pattern': null,
    'media-feature-range-notation': 'prefix',
  },
};
