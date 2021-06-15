const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	challenge: path.join(__dirname, 'src', 'challenge'),
	settings: path.join(__dirname, 'src', 'settings'),
}

module.exports = webpackConfig
