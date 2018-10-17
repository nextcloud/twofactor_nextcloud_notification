const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
	entry: [
		path.join(__dirname, 'main-challenge.js')
	],
	output: {
		path: path.resolve(__dirname, '../js'),
		publicPath: '/js/'
	},
	module: {
		rules: [
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/
			}
		]
	},
	plugins: [new VueLoaderPlugin()]
};