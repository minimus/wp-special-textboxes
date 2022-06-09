const path = require('path')
const { DefinePlugin } = require('webpack')
const TerserPlugin = require('terser-webpack-plugin')
// const WebpackAssetsManifest = require('webpack-assets-manifest')

// eslint-disable-next-line no-undef
const isDebug = !!process.env.DEV

module.exports = [
	{
		mode: isDebug ? 'development' : 'production',
		target: 'web',
		entry: {
			admin: ['./js-src/admin/index.tsx'],
		},
		output: {
			// eslint-disable-next-line no-undef
			path: path.resolve(__dirname, 'plugin/js'),
			publicPath: '/',
			filename: '[name].js',
			// chunkFilename: '[id]-[chunkhash].js',
			// clean: true,
		},
		devtool: 'source-map',
		devServer: {
			port: 9000,
			historyApiFallback: true,
		},
		module: {
			rules: [
				{
					test: /\.ts.?$/,
					exclude: /node_modules/,
					use: [{ loader: 'ts-loader' }],
				},
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: [{ loader: 'babel-loader' }],
				},
				{
					test: /\.css$/,
					use: [
						{ loader: 'style-loader' },
						{
							loader: 'css-loader',
							options: {
								importLoaders: 1,
							},
						},
						{
							loader: 'postcss-loader',
							options: {
								config: {
									path: './postcss.config.js',
								},
							},
						},
					],
				},
				{ test: /\.gif$/, use: [{ loader: 'url-loader?mimetype=image/gif' }] },
				{ test: /\.png$/, use: [{ loader: 'url-loader?mimetype=image/png' }] },
			],
		},
		resolve: {
			modules: ['node_modules', 'js-src'],
			extensions: ['.js', '.jsx', '.ts', '.tsx'],
			mainFields: ['browser', 'jsnext:main', 'main'],
		},
		optimization: {
			/* splitChunks: {
			chunks: 'all',
			maxInitialRequests: Infinity,
			minSize: 0,
			cacheGroups: {
				vendor: {
					test: /[\\/]node_modules[\\/]/,
					name(module) {
						// get the name. E.g. node_modules/packageName/not/this/part.js
						// or node_modules/packageName
						const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1];

						// npm package names are URL-safe, but some servers don't like @ symbols
						return `npm.${packageName.replace('@', '')}`
					},
				},
			},
		}, */
			minimize: !isDebug,
			...(isDebug ? {} : { minimizer: [new TerserPlugin()] }),
		},
		plugins: [
			new DefinePlugin({
				__DEV__: isDebug,
			}),
			// new WebpackAssetsManifest({}),
		],
	},
	{
		mode: isDebug ? 'development' : 'production',
		target: 'web',
		entry: {
			client: ['./js-src/client/core/index.js'],
			'editor.plugin': './js-src/editor/classic/editor.plugin.js',
		},
		output: {
			// eslint-disable-next-line no-undef
			path: path.resolve(__dirname, 'plugin/js'),
			publicPath: '/',
			filename: '[name].js',
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: [{ loader: 'babel-loader' }],
				},
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: [{ loader: 'babel-loader' }],
				},
				{
					test: /\.css$/,
					use: [
						{ loader: 'style-loader' },
						{
							loader: 'css-loader',
							options: {
								importLoaders: 1,
							},
						},
						{
							loader: 'postcss-loader',
							options: {
								config: {
									path: './postcss.config.js',
								},
							},
						},
					],
				},
				{ test: /\.gif$/, use: [{ loader: 'url-loader?mimetype=image/gif' }] },
				{ test: /\.png$/, use: [{ loader: 'url-loader?mimetype=image/png' }] },
			],
		},
		devtool: 'source-map',
		resolve: {
			modules: ['node_modules', 'js-src'],
			extensions: ['.js', '.jsx', '.ts', '.tsx'],
			mainFields: ['browser', 'jsnext:main', 'main'],
		},
		optimization: {
			minimize: !isDebug,
			...(isDebug ? {} : { minimizer: [new TerserPlugin()] }),
		},
	},
]
