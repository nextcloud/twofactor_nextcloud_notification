/**
 * SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	challenge: path.join(__dirname, 'src', 'challenge'),
	settings: path.join(__dirname, 'src', 'settings'),
}

module.exports = webpackConfig
