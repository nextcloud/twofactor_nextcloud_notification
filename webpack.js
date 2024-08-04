/**
 * SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')
const TerserPlugin = require('terser-webpack-plugin')
const WebpackSPDXPlugin = require('./build-js/WebpackSPDXPlugin.js')

webpackConfig.entry = {
	challenge: path.join(__dirname, 'src', 'challenge'),
	settings: path.join(__dirname, 'src', 'settings'),
}

// block creation of LICENSE.txt files now replaced with .license files
webpackConfig.optimization.minimizer = [new TerserPlugin({
	extractComments: false,
	terserOptions: {
		format: {
			comments: false,
		},
	},
})]

webpackConfig.plugins = [
	...webpackConfig.plugins,
	// Generate reuse license files
	new WebpackSPDXPlugin({
		override: {
			// TODO: Remove if they fixed the license in the package.json
			'@nextcloud/axios': 'GPL-3.0-or-later',
			'@nextcloud/vue': 'AGPL-3.0-or-later',
			'nextcloud-vue-collections': 'AGPL-3.0-or-later',
		}
	}),
]

module.exports = webpackConfig
