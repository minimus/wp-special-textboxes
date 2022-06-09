module.exports = {
	parser: 'babel-eslint',

	extends: [
		// 'airbnb',
		// 'plugin:flowtype/recommended',
		'plugin:css-modules/recommended',
		'prettier',
		// 'prettier/flowtype',
		// 'prettier/react',
	],

	plugins: [/* 'flowtype', */ 'css-modules', 'prettier'],

	globals: {
		__DEV__: true,
	},

	env: {
		browser: true,
	},

	rules: {
		indent: 0,

		// `js` and `jsx` are common extensions
		// `mjs` is for `universal-router` only, for now
		'import/extensions': [
			'error',
			'always',
			{
				js: 'never',
				jsx: 'never',
				mjs: 'never',
				ts: 'never',
				tsx: 'never',
			},
		],

		// Not supporting nested package.json yet
		// https://github.com/benmosher/eslint-plugin-import/issues/458
		'import/no-extraneous-dependencies': 'off',

		// Recommend not to leave any console.log in your code
		// Use console.error, console.warn and console.info instead
		'no-console': [
			'error',
			{
				allow: ['warn', 'error', 'info'],
			},
		],

		// Allow js files to use jsx syntax, too
		'react/jsx-filename-extension': ['error', { extensions: ['.js', '.jsx'] }],

		// Automatically convert pure class to function by
		// babel-plugin-transform-react-pure-class-to-function
		// https://github.com/kriasoft/react-starter-kit/pull/961
		'react/prefer-stateless-function': 'off',

		// Enforce state initialization style
		// https://github.com/yannickcr/eslint-plugin-react/blob/master/docs/rules/state-in-constructor.md
		'react/state-in-constructor': 'off',

		// Enforces where React component static properties should be positioned
		// https://github.com/yannickcr/eslint-plugin-react/blob/master/docs/rules/static-property-placement.md
		'react/static-property-placement': 'off',

		// Require CamelCase naming
		camelcase: 'off',

		// Prefer default export
		// https://github.com/benmosher/eslint-plugin-import/blob/master/docs/rules/prefer-default-export.md
		'import/prefer-default-export': 'off',

		// ESLint plugin for prettier formatting
		// https://github.com/prettier/eslint-plugin-prettier
		'prettier/prettier': [
			'error',
			{
				// https://github.com/prettier/prettier#options
				singleQuote: true,
				trailingComma: 'all',
				useTabs: true,
				tabWidth: 4,
				printWidth: 120,
				semi: false,
			},
		],
	},

	settings: {
		// Allow absolute paths in imports, e.g. import Button from 'components/Button'
		// https://github.com/benmosher/eslint-plugin-import/tree/master/resolvers
		'import/resolver': {
			node: {
				moduleDirectory: ['node_modules', 'src'],
			},
		},
	},

	overrides: [
		{
			files: ['**/*.ts', '**/*.tsx'],
			parser: '@typescript-eslint/parser',
			plugins: ['@typescript-eslint', 'import', 'prettier'],
			extends: [
				'eslint:recommended',
				// 'prettier',
				// 'airbnb',
				// 'airbnb-typescript',
				// 'airbnb/hooks',
				'plugin:@typescript-eslint/recommended',
				'plugin:@typescript-eslint/recommended-requiring-type-checking',
				'plugin:import/recommended',
				'plugin:import/typescript',
				'plugin:react/recommended',
				'plugin:prettier/recommended',
			],
			parserOptions: {
				ecmaVersion: 'latest',
				sourceType: 'module',
				ecmaFeatures: {
					jsx: true,
				},
				project: './tsconfig.json',
			},
			rules: {
				// Allow js files to use jsx syntax, too
				'react/jsx-filename-extension': ['error', { extensions: ['.js', '.jsx', '.ts', '.tsx'] }],
			}
		},
	],
}
