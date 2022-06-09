module.exports = {
	presets: ['@babel/preset-env', '@babel/preset-flow', '@babel/preset-react'],
	plugins: [
		['@babel/plugin-proposal-decorators', { legacy: true }],
		'@babel/plugin-proposal-class-properties',
		'@babel/plugin-proposal-private-methods',
		'@babel/plugin-proposal-private-property-in-object',
		'@babel/plugin-proposal-export-default-from',
		'@babel/plugin-proposal-export-namespace-from',
		'@babel/plugin-proposal-logical-assignment-operators',
		'@babel/plugin-proposal-nullish-coalescing-operator',
		'@babel/plugin-proposal-optional-chaining',
		['@babel/plugin-proposal-pipeline-operator', { proposal: 'minimal' }],
		'@babel/plugin-syntax-dynamic-import',
		'babel-plugin-styled-components',
		'babel-plugin-transform-object-hasown',
	],
	ignore: ['node_modules', 'js', 'css'],
}