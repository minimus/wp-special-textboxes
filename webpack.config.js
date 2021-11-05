const path = require('path')

module.exports = {
    mode: 'production',
    entry: {
        'jquery.stb': ['@babel/polyfill/noConflict', './js-src/jquery.stb.js'],
        'wstb.admin': ['@babel/polyfill/noConflict', './js-src/wstb.admin.js'],
        'wstb.all': ['@babel/polyfill/noConflict', './js-src/wstb.all.js'],
        'wstb.edit': ['@babel/polyfill/noConflict', './js-src/wstb.edit.js'],
        'wstb.editor.plugin': ['@babel/polyfill/noConflict', './js-src/wstb.editor.plugin.js'],
        wstb: ['@babel/polyfill/noConflict', './js-src/wstb.js'],
        'wstb.themes': ['@babel/polyfill/noConflict', './js-src/wstb.themes.js'],
    },
    output: {
        path: path.resolve(__dirname, 'js'),
        filename: '[name].min.js',
        publicPath: '/',
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [{ loader: 'babel-loader' }],
            },
        ]
    },
    devtool: 'source-map',
}